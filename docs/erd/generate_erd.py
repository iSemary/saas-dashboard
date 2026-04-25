#!/usr/bin/env python3
"""
ERD Diagram Generator
Generates an Entity Relationship Diagram from the ERD markdown files.
"""

import re
from pathlib import Path
from graphviz import Digraph


def parse_erd_file(file_path):
    """Parse an ERD markdown file and extract table information."""
    tables = []
    current_module = "Core"
    
    with open(file_path, 'r') as f:
        content = f.read()
    
    lines = content.split('\n')
    
    for line in lines:
        line = line.strip()
        
        # Detect module headers
        if line.startswith('### ') and 'Module' in line:
            current_module = line.replace('### ', '').replace(' Module', '')
        
        # Detect table headers
        elif line.startswith('#### '):
            table_name = line.replace('#### ', '')
            tables.append({
                'name': table_name,
                'module': current_module,
                'columns': [],
                'pk': None,
                'fks': [],
                'indexes': []
            })
        
        # Parse table properties
        elif line.startswith('- **PK**'):
            if tables:
                pk = line.split(':')[1].strip()
                tables[-1]['pk'] = pk
        
        elif line.startswith('- **Columns**'):
            if tables:
                columns_str = line.split(':')[1].strip()
                columns = [col.strip() for col in columns_str.split(',')]
                tables[-1]['columns'] = columns
        
        elif line.startswith('- **FK**'):
            if tables:
                fk_str = line.split(':')[1].strip()
                # Parse FK: table_id → referenced_table (action)
                fk_match = re.match(r'(\w+)\s*→\s*(\w+)(?:\s*\(([^)]+)\))?', fk_str)
                if fk_match:
                    tables[-1]['fks'].append({
                        'column': fk_match.group(1),
                        'ref_table': fk_match.group(2),
                        'action': fk_match.group(3) if fk_match.group(3) else ''
                    })
        
        elif line.startswith('- **Indexes**'):
            if tables:
                indexes_str = line.split(':')[1].strip()
                indexes = [idx.strip() for idx in indexes_str.split(',')]
                tables[-1]['indexes'] = indexes
    
    return tables


def create_erd_diagram(tables_by_db_type, output_path):
    """Create an ERD diagram using Graphviz."""
    dot = Digraph(comment='SaaS Dashboard ERD', format='jpg')
    dot.attr(rankdir='LR', splines='ortho', nodesep='0.3', ranksep='0.8')
    dot.attr('node', shape='plaintext', fontname='Arial', fontsize='9')
    dot.attr('edge', fontname='Arial', fontsize='8')
    dot.attr(size='20,10')  # Set aspect ratio to landscape (width, height)
    
    # Color scheme for different database types
    colors = {
        'landlord': '#e8f4e8',  # Light green
        'tenant': '#e8f0f8',     # Light blue
        'shared': '#f8f0e8'      # Light orange
    }
    
    # Create subgraphs for each database type
    for db_type, tables in tables_by_db_type.items():
        with dot.subgraph(name=f'cluster_{db_type}') as sub:
            sub.attr(label=f'{db_type.capitalize()} Database', style='rounded', color=colors[db_type])
            sub.attr('node', shape='plaintext')
            
            # Group tables by module
            modules = {}
            for table in tables:
                module = table['module']
                if module not in modules:
                    modules[module] = []
                modules[module].append(table)
            
            # Create tables
            for module, module_tables in modules.items():
                with sub.subgraph(name=f'cluster_{db_type}_{module}') as module_sub:
                    module_sub.attr(label=module, style='rounded,dashed')
                    
                    for table in module_tables:
                        # Create table node
                        table_label = f'<<TABLE BORDER="1" CELLBORDER="1" CELLSPACING="0">'
                        table_label += f'<TR><TD BGCOLOR="#4a90e2" PORT="title"><FONT COLOR="white"><B>{table["name"]}</B></FONT></TD></TR>'
                        
                        # Add columns
                        for col in table['columns']:
                            col_text = col
                            if table['pk'] and col.startswith(table['pk']):
                                col_text = f'<B>{col}</B> (PK)'
                            table_label += f'<TR><TD>{col_text}</TD></TR>'
                        
                        # Add foreign keys
                        for fk in table['fks']:
                            table_label += f'<TR><TD><FONT COLOR="#666">→ {fk["ref_table"]}</FONT></TD></TR>'
                        
                        table_label += '</TABLE>>'
                        
                        sub.node(table['name'], label=table_label)
    
    # Add relationships (edges) between tables
    for db_type, tables in tables_by_db_type.items():
        for table in tables:
            for fk in table['fks']:
                ref_table = fk['ref_table']
                # Find the referenced table
                found = False
                for other_db, other_tables in tables_by_db_type.items():
                    for other_table in other_tables:
                        if other_table['name'] == ref_table:
                            # Create edge
                            edge_label = fk['action'] if fk['action'] else ''
                            dot.edge(
                                f'{table["name"]}:{fk["column"]}',
                                f'{other_table["name"]}',
                                label=edge_label,
                                color='#666',
                                style='solid'
                            )
                            found = True
                            break
                    if found:
                        break
    
    # Render the diagram
    output_file = str(output_path.parent / output_path.stem)
    dot.render(output_file, cleanup=True)
    print(f"ERD diagram generated: {output_path}")


def main():
    """Main function to generate the ERD diagram."""
    erd_dir = Path(__file__).parent
    
    # Parse all three ERD files
    landlord_tables = parse_erd_file(erd_dir / 'landlord-erd.md')
    tenant_tables = parse_erd_file(erd_dir / 'tenant-erd.md')
    shared_tables = parse_erd_file(erd_dir / 'shared-erd.md')
    
    # Organize by database type
    tables_by_db_type = {
        'landlord': landlord_tables,
        'tenant': tenant_tables,
        'shared': shared_tables
    }
    
    # Generate diagram
    output_path = erd_dir / 'erd.jpg'
    create_erd_diagram(tables_by_db_type, output_path)
    
    print(f"Total tables: {len(landlord_tables) + len(tenant_tables) + len(shared_tables)}")
    print(f"  - Landlord: {len(landlord_tables)}")
    print(f"  - Tenant: {len(tenant_tables)}")
    print(f"  - Shared: {len(shared_tables)}")


if __name__ == '__main__':
    main()
