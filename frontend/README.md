# Tenant Dashboard Frontend

A modern Next.js 16 frontend for the SaaS Tenant Dashboard, built with shadcn/ui components and following the same architecture as the dev-tools project.

## Technology Stack

- **Framework**: Next.js 16.1.4
- **React**: 19.2.3
- **TypeScript**: 5.x
- **UI Components**: shadcn/ui (Radix UI)
- **Styling**: Tailwind CSS 4
- **Form Handling**: React Hook Form + Zod
- **HTTP Client**: Axios
- **Notifications**: Sonner

## Getting Started

### Prerequisites

- Node.js 18+
- npm or yarn

### Installation

```bash
npm install
```

### Environment Configuration

Create a `.env.local` file:

```env
NEXT_PUBLIC_API_BASE_URL=http://customer1.saas.test/api
```

### Development

```bash
npm run dev
```

The application will be available at `http://localhost:3000`

### Build

```bash
npm run build
npm start
```

## Project Structure

```
frontend/
├── src/
│   ├── app/                    # Next.js App Router
│   │   ├── dashboard/         # Dashboard pages
│   │   ├── login/             # Authentication pages
│   │   ├── layout.tsx         # Root layout
│   │   └── globals.css        # Global styles
│   ├── components/
│   │   ├── dashboard/         # Dashboard components
│   │   ├── ui/               # shadcn/ui components
│   │   └── two-factor/       # 2FA components
│   ├── context/
│   │   └── auth-context.tsx  # Auth context provider
│   └── lib/
│       ├── api.ts            # API client
│       ├── utils.ts          # Utility functions
│       └── ...               # API helpers
├── components.json           # shadcn/ui config
├── package.json
├── tsconfig.json
└── next.config.ts
```

## Features

- **Authentication**: Login with 2FA support
- **Dashboard**: Overview with statistics and activity
- **User Management**: CRUD operations for users
- **Role Management**: Create and manage roles with permissions
- **Permission Management**: View all available permissions
- **Settings**: General settings and 2FA configuration
- **Responsive Design**: Mobile-friendly layout

## API Integration

The frontend communicates with the Laravel backend API. Ensure the backend is running and CORS is properly configured.

## Authentication Flow

1. User logs in with email/password
2. If 2FA is enabled, user is redirected to verification page
3. After successful authentication, user is redirected to dashboard
4. JWT token is stored in localStorage
5. Token is automatically included in API requests

## Development Notes

- The frontend follows the same architecture as the dev-tools project
- All components use shadcn/ui for consistent styling
- TypeScript is used throughout for type safety
- React Hook Form + Zod is used for form validation
