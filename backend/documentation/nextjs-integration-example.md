# Next.js Integration Example

This document provides a complete example of how to integrate the Static Pages API with a Next.js website, including multi-language support and Privacy Policy implementation.

## Project Structure

```
nextjs-website/
├── components/
│   ├── LanguageSwitcher.js
│   ├── PrivacyPolicy.js
│   └── StaticPage.js
├── pages/
│   ├── privacy-policy.js
│   ├── terms-of-service.js
│   └── about-us.js
├── lib/
│   ├── api.js
│   └── translations.js
├── styles/
│   └── globals.css
└── package.json
```

## Installation

```bash
# Create Next.js project
npx create-next-app@latest nextjs-website
cd nextjs-website

# Install additional dependencies
npm install axios swr
```

## API Configuration

### lib/api.js

```javascript
import axios from 'axios';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Add language parameter to all requests
    const language = localStorage.getItem('language') || 'en';
    config.params = { ...config.params, lang: language };
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    console.error('API Error:', error);
    return Promise.reject(error);
  }
);

export const staticPagesAPI = {
  // Get all pages
  getAllPages: (params = {}) => {
    return api.get('/static-pages', { params });
  },

  // Get page by slug
  getPageBySlug: (slug, params = {}) => {
    return api.get(`/static-pages/${slug}`, { params });
  },

  // Get pages by type
  getPagesByType: (type, params = {}) => {
    return api.get(`/static-pages/type/${type}`, { params });
  },

  // Search pages
  searchPages: (query, params = {}) => {
    return api.get('/static-pages/search', { 
      params: { q: query, ...params } 
    });
  },

  // Get available languages
  getLanguages: () => {
    return api.get('/static-pages/languages');
  },
};

export default api;
```

## Translation Utilities

### lib/translations.js

```javascript
// Default translations fallback
const defaultTranslations = {
  en: {
    'privacy_policy': 'Privacy Policy',
    'terms_of_service': 'Terms of Service',
    'about_us': 'About Us',
    'loading': 'Loading...',
    'error': 'An error occurred',
    'page_not_found': 'Page not found',
    'back_to_home': 'Back to Home',
    'language': 'Language',
    'search': 'Search',
    'search_placeholder': 'Search pages...',
  },
  ar: {
    'privacy_policy': 'سياسة الخصوصية',
    'terms_of_service': 'شروط الخدمة',
    'about_us': 'من نحن',
    'loading': 'جاري التحميل...',
    'error': 'حدث خطأ',
    'page_not_found': 'الصفحة غير موجودة',
    'back_to_home': 'العودة للرئيسية',
    'language': 'اللغة',
    'search': 'بحث',
    'search_placeholder': 'البحث في الصفحات...',
  },
  fr: {
    'privacy_policy': 'Politique de Confidentialité',
    'terms_of_service': 'Conditions d\'Utilisation',
    'about_us': 'À Propos de Nous',
    'loading': 'Chargement...',
    'error': 'Une erreur s\'est produite',
    'page_not_found': 'Page non trouvée',
    'back_to_home': 'Retour à l\'accueil',
    'language': 'Langue',
    'search': 'Rechercher',
    'search_placeholder': 'Rechercher dans les pages...',
  },
};

export const getTranslation = (key, language = 'en') => {
  return defaultTranslations[language]?.[key] || defaultTranslations.en[key] || key;
};

export const getCurrentLanguage = () => {
  if (typeof window !== 'undefined') {
    return localStorage.getItem('language') || 'en';
  }
  return 'en';
};

export const setLanguage = (language) => {
  if (typeof window !== 'undefined') {
    localStorage.setItem('language', language);
    window.location.reload();
  }
};

export const getLanguageDirection = (language) => {
  const rtlLanguages = ['ar', 'he', 'fa', 'ur'];
  return rtlLanguages.includes(language) ? 'rtl' : 'ltr';
};
```

## Language Switcher Component

### components/LanguageSwitcher.js

```javascript
import { useState, useEffect } from 'react';
import { staticPagesAPI } from '../lib/api';
import { getCurrentLanguage, setLanguage, getLanguageDirection } from '../lib/translations';

export default function LanguageSwitcher() {
  const [languages, setLanguages] = useState([]);
  const [currentLang, setCurrentLang] = useState('en');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const loadLanguages = async () => {
      try {
        const response = await staticPagesAPI.getLanguages();
        if (response.data.success) {
          setLanguages(response.data.data);
          setCurrentLang(getCurrentLanguage());
        }
      } catch (error) {
        console.error('Failed to load languages:', error);
      } finally {
        setLoading(false);
      }
    };

    loadLanguages();
  }, []);

  const handleLanguageChange = (langCode) => {
    setLanguage(langCode);
    setCurrentLang(langCode);
  };

  if (loading) {
    return <div className="language-switcher loading">Loading languages...</div>;
  }

  return (
    <div className="language-switcher">
      <select 
        value={currentLang} 
        onChange={(e) => handleLanguageChange(e.target.value)}
        className="language-select"
      >
        {languages.map(lang => (
          <option key={lang.code} value={lang.code}>
            {lang.flag} {lang.native_name}
          </option>
        ))}
      </select>
    </div>
  );
}
```

## Static Page Component

### components/StaticPage.js

```javascript
import { useState, useEffect } from 'react';
import { useRouter } from 'next/router';
import { staticPagesAPI } from '../lib/api';
import { getTranslation, getCurrentLanguage, getLanguageDirection } from '../lib/translations';

export default function StaticPage({ slug, type = 'page' }) {
  const [page, setPage] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const router = useRouter();
  const language = getCurrentLanguage();
  const direction = getLanguageDirection(language);

  useEffect(() => {
    const loadPage = async () => {
      try {
        setLoading(true);
        setError(null);
        
        const response = await staticPagesAPI.getPageBySlug(slug, { lang: language });
        
        if (response.data.success) {
          setPage(response.data.data);
        } else {
          setError('Page not found');
        }
      } catch (err) {
        console.error('Failed to load page:', err);
        setError('Failed to load page');
      } finally {
        setLoading(false);
      }
    };

    if (slug) {
      loadPage();
    }
  }, [slug, language]);

  if (loading) {
    return (
      <div className="static-page loading" dir={direction}>
        <div className="loading-spinner">
          {getTranslation('loading', language)}
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="static-page error" dir={direction}>
        <div className="error-message">
          <h1>{getTranslation('error', language)}</h1>
          <p>{error}</p>
          <button onClick={() => router.push('/')}>
            {getTranslation('back_to_home', language)}
          </button>
        </div>
      </div>
    );
  }

  if (!page) {
    return (
      <div className="static-page not-found" dir={direction}>
        <div className="not-found-message">
          <h1>{getTranslation('page_not_found', language)}</h1>
          <button onClick={() => router.push('/')}>
            {getTranslation('back_to_home', language)}
          </button>
        </div>
      </div>
    );
  }

  const content = page.content?.content?.value || '';
  const title = page.content?.title?.value || page.name;

  return (
    <div className="static-page" dir={direction}>
      <div className="page-header">
        <h1 className="page-title">{title}</h1>
        {page.description && (
          <p className="page-description">{page.description}</p>
        )}
      </div>
      
      <div className="page-content">
        <div 
          className="content-body"
          dangerouslySetInnerHTML={{ __html: content }}
        />
      </div>
      
      {page.seo && (
        <div className="page-meta">
          <meta name="description" content={page.seo.description} />
          <meta name="keywords" content={page.seo.keywords} />
        </div>
      )}
    </div>
  );
}
```

## Privacy Policy Page

### pages/privacy-policy.js

```javascript
import Head from 'next/head';
import { useState, useEffect } from 'react';
import { staticPagesAPI } from '../lib/api';
import { getCurrentLanguage, getLanguageDirection } from '../lib/translations';
import LanguageSwitcher from '../components/LanguageSwitcher';
import StaticPage from '../components/StaticPage';

export default function PrivacyPolicy() {
  const [page, setPage] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const language = getCurrentLanguage();
  const direction = getLanguageDirection(language);

  useEffect(() => {
    const loadPrivacyPolicy = async () => {
      try {
        setLoading(true);
        setError(null);
        
        const response = await staticPagesAPI.getPageBySlug('privacy-policy', { lang: language });
        
        if (response.data.success) {
          setPage(response.data.data);
        } else {
          setError('Privacy Policy not found');
        }
      } catch (err) {
        console.error('Failed to load privacy policy:', err);
        setError('Failed to load privacy policy');
      } finally {
        setLoading(false);
      }
    };

    loadPrivacyPolicy();
  }, [language]);

  if (loading) {
    return (
      <div className="privacy-policy loading" dir={direction}>
        <Head>
          <title>Privacy Policy - Loading</title>
        </Head>
        <div className="loading-spinner">Loading...</div>
      </div>
    );
  }

  if (error || !page) {
    return (
      <div className="privacy-policy error" dir={direction}>
        <Head>
          <title>Privacy Policy - Error</title>
        </Head>
        <div className="error-message">
          <h1>Error</h1>
          <p>{error || 'Privacy Policy not found'}</p>
        </div>
      </div>
    );
  }

  const content = page.content?.content?.value || '';
  const title = page.content?.title?.value || page.name;
  const metaDescription = page.meta_description || page.description;

  return (
    <div className="privacy-policy" dir={direction}>
      <Head>
        <title>{title}</title>
        <meta name="description" content={metaDescription} />
        <meta name="keywords" content={page.meta_keywords} />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="/favicon.ico" />
      </Head>

      <header className="page-header">
        <div className="container">
          <div className="header-content">
            <h1 className="page-title">{title}</h1>
            <LanguageSwitcher />
          </div>
        </div>
      </header>

      <main className="page-main">
        <div className="container">
          <div className="page-content">
            <div 
              className="content-body"
              dangerouslySetInnerHTML={{ __html: content }}
            />
          </div>
        </div>
      </main>

      <footer className="page-footer">
        <div className="container">
          <p>&copy; 2025 Your Company. All rights reserved.</p>
        </div>
      </footer>
    </div>
  );
}
```

## Terms of Service Page

### pages/terms-of-service.js

```javascript
import Head from 'next/head';
import { useState, useEffect } from 'react';
import { staticPagesAPI } from '../lib/api';
import { getCurrentLanguage, getLanguageDirection } from '../lib/translations';
import LanguageSwitcher from '../components/LanguageSwitcher';

export default function TermsOfService() {
  const [page, setPage] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const language = getCurrentLanguage();
  const direction = getLanguageDirection(language);

  useEffect(() => {
    const loadTermsOfService = async () => {
      try {
        setLoading(true);
        setError(null);
        
        const response = await staticPagesAPI.getPageBySlug('terms-of-service', { lang: language });
        
        if (response.data.success) {
          setPage(response.data.data);
        } else {
          setError('Terms of Service not found');
        }
      } catch (err) {
        console.error('Failed to load terms of service:', err);
        setError('Failed to load terms of service');
      } finally {
        setLoading(false);
      }
    };

    loadTermsOfService();
  }, [language]);

  if (loading) {
    return (
      <div className="terms-of-service loading" dir={direction}>
        <Head>
          <title>Terms of Service - Loading</title>
        </Head>
        <div className="loading-spinner">Loading...</div>
      </div>
    );
  }

  if (error || !page) {
    return (
      <div className="terms-of-service error" dir={direction}>
        <Head>
          <title>Terms of Service - Error</title>
        </Head>
        <div className="error-message">
          <h1>Error</h1>
          <p>{error || 'Terms of Service not found'}</p>
        </div>
      </div>
    );
  }

  const content = page.content?.content?.value || '';
  const title = page.content?.title?.value || page.name;
  const metaDescription = page.meta_description || page.description;

  return (
    <div className="terms-of-service" dir={direction}>
      <Head>
        <title>{title}</title>
        <meta name="description" content={metaDescription} />
        <meta name="keywords" content={page.meta_keywords} />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="/favicon.ico" />
      </Head>

      <header className="page-header">
        <div className="container">
          <div className="header-content">
            <h1 className="page-title">{title}</h1>
            <LanguageSwitcher />
          </div>
        </div>
      </header>

      <main className="page-main">
        <div className="container">
          <div className="page-content">
            <div 
              className="content-body"
              dangerouslySetInnerHTML={{ __html: content }}
            />
          </div>
        </div>
      </main>

      <footer className="page-footer">
        <div className="container">
          <p>&copy; 2025 Your Company. All rights reserved.</p>
        </div>
      </footer>
    </div>
  );
}
```

## About Us Page

### pages/about-us.js

```javascript
import Head from 'next/head';
import { useState, useEffect } from 'react';
import { staticPagesAPI } from '../lib/api';
import { getCurrentLanguage, getLanguageDirection } from '../lib/translations';
import LanguageSwitcher from '../components/LanguageSwitcher';

export default function AboutUs() {
  const [page, setPage] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const language = getCurrentLanguage();
  const direction = getLanguageDirection(language);

  useEffect(() => {
    const loadAboutUs = async () => {
      try {
        setLoading(true);
        setError(null);
        
        const response = await staticPagesAPI.getPageBySlug('about-us', { lang: language });
        
        if (response.data.success) {
          setPage(response.data.data);
        } else {
          setError('About Us page not found');
        }
      } catch (err) {
        console.error('Failed to load about us page:', err);
        setError('Failed to load about us page');
      } finally {
        setLoading(false);
      }
    };

    loadAboutUs();
  }, [language]);

  if (loading) {
    return (
      <div className="about-us loading" dir={direction}>
        <Head>
          <title>About Us - Loading</title>
        </Head>
        <div className="loading-spinner">Loading...</div>
      </div>
    );
  }

  if (error || !page) {
    return (
      <div className="about-us error" dir={direction}>
        <Head>
          <title>About Us - Error</title>
        </Head>
        <div className="error-message">
          <h1>Error</h1>
          <p>{error || 'About Us page not found'}</p>
        </div>
      </div>
    );
  }

  const content = page.content?.content?.value || '';
  const title = page.content?.title?.value || page.name;
  const metaDescription = page.meta_description || page.description;

  return (
    <div className="about-us" dir={direction}>
      <Head>
        <title>{title}</title>
        <meta name="description" content={metaDescription} />
        <meta name="keywords" content={page.meta_keywords} />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="icon" href="/favicon.ico" />
      </Head>

      <header className="page-header">
        <div className="container">
          <div className="header-content">
            <h1 className="page-title">{title}</h1>
            <LanguageSwitcher />
          </div>
        </div>
      </header>

      <main className="page-main">
        <div className="container">
          <div className="page-content">
            <div 
              className="content-body"
              dangerouslySetInnerHTML={{ __html: content }}
            />
          </div>
        </div>
      </main>

      <footer className="page-footer">
        <div className="container">
          <p>&copy; 2025 Your Company. All rights reserved.</p>
        </div>
      </footer>
    </div>
  );
}
```

## Global Styles

### styles/globals.css

```css
/* Global Styles */
* {
  box-sizing: border-box;
  padding: 0;
  margin: 0;
}

html,
body {
  max-width: 100vw;
  overflow-x: hidden;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
    'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
    sans-serif;
  line-height: 1.6;
  color: #333;
}

body {
  color: rgb(var(--foreground-rgb));
  background: linear-gradient(
      to bottom,
      transparent,
      rgb(var(--background-end-rgb))
    )
    rgb(var(--background-start-rgb));
}

a {
  color: inherit;
  text-decoration: none;
}

/* Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Language Switcher */
.language-switcher {
  display: flex;
  align-items: center;
  gap: 10px;
}

.language-select {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
  font-size: 14px;
  cursor: pointer;
}

.language-select:focus {
  outline: none;
  border-color: #0070f3;
}

/* Page Header */
.page-header {
  background: #f8f9fa;
  padding: 20px 0;
  border-bottom: 1px solid #e9ecef;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #333;
  margin: 0;
}

/* Page Main */
.page-main {
  padding: 40px 0;
  min-height: 60vh;
}

.page-content {
  max-width: 800px;
  margin: 0 auto;
}

.content-body {
  font-size: 1.1rem;
  line-height: 1.8;
  color: #555;
}

.content-body h1,
.content-body h2,
.content-body h3,
.content-body h4,
.content-body h5,
.content-body h6 {
  margin: 30px 0 15px 0;
  color: #333;
  font-weight: 600;
}

.content-body h1 {
  font-size: 2rem;
}

.content-body h2 {
  font-size: 1.75rem;
}

.content-body h3 {
  font-size: 1.5rem;
}

.content-body p {
  margin-bottom: 20px;
}

.content-body ul,
.content-body ol {
  margin: 20px 0;
  padding-left: 30px;
}

.content-body li {
  margin-bottom: 10px;
}

.content-body a {
  color: #0070f3;
  text-decoration: underline;
}

.content-body a:hover {
  color: #0051a2;
}

.content-body blockquote {
  border-left: 4px solid #0070f3;
  padding-left: 20px;
  margin: 20px 0;
  font-style: italic;
  color: #666;
}

.content-body code {
  background: #f4f4f4;
  padding: 2px 6px;
  border-radius: 3px;
  font-family: 'Courier New', monospace;
  font-size: 0.9em;
}

.content-body pre {
  background: #f4f4f4;
  padding: 20px;
  border-radius: 5px;
  overflow-x: auto;
  margin: 20px 0;
}

.content-body pre code {
  background: none;
  padding: 0;
}

/* Page Footer */
.page-footer {
  background: #333;
  color: white;
  padding: 20px 0;
  text-align: center;
  margin-top: 60px;
}

/* Loading States */
.loading {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 50vh;
}

.loading-spinner {
  font-size: 1.2rem;
  color: #666;
}

/* Error States */
.error {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 50vh;
}

.error-message {
  text-align: center;
  padding: 40px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.error-message h1 {
  color: #dc3545;
  margin-bottom: 20px;
}

.error-message p {
  color: #666;
  margin-bottom: 30px;
}

.error-message button {
  background: #0070f3;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

.error-message button:hover {
  background: #0051a2;
}

/* Not Found States */
.not-found {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 50vh;
}

.not-found-message {
  text-align: center;
  padding: 40px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.not-found-message h1 {
  color: #333;
  margin-bottom: 20px;
}

.not-found-message button {
  background: #0070f3;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

.not-found-message button:hover {
  background: #0051a2;
}

/* RTL Support */
[dir="rtl"] {
  text-align: right;
}

[dir="rtl"] .header-content {
  flex-direction: row-reverse;
}

[dir="rtl"] .content-body ul,
[dir="rtl"] .content-body ol {
  padding-right: 30px;
  padding-left: 0;
}

[dir="rtl"] .content-body blockquote {
  border-right: 4px solid #0070f3;
  border-left: none;
  padding-right: 20px;
  padding-left: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .container {
    padding: 0 15px;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 20px;
    text-align: center;
  }
  
  .page-main {
    padding: 20px 0;
  }
  
  .content-body {
    font-size: 1rem;
  }
  
  .content-body h1 {
    font-size: 1.75rem;
  }
  
  .content-body h2 {
    font-size: 1.5rem;
  }
  
  .content-body h3 {
    font-size: 1.25rem;
  }
}

@media (max-width: 480px) {
  .page-title {
    font-size: 1.75rem;
  }
  
  .content-body {
    font-size: 0.95rem;
  }
  
  .error-message,
  .not-found-message {
    padding: 20px;
  }
}
```

## Environment Configuration

### .env.local

```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api
NEXT_PUBLIC_SITE_NAME=Your SaaS Dashboard
NEXT_PUBLIC_DEFAULT_LANGUAGE=en
```

## Package.json

```json
{
  "name": "nextjs-website",
  "version": "0.1.0",
  "private": true,
  "scripts": {
    "dev": "next dev",
    "build": "next build",
    "start": "next start",
    "lint": "next lint"
  },
  "dependencies": {
    "next": "14.0.0",
    "react": "^18.0.0",
    "react-dom": "^18.0.0",
    "axios": "^1.6.0",
    "swr": "^2.2.0"
  },
  "devDependencies": {
    "eslint": "^8.0.0",
    "eslint-config-next": "14.0.0"
  }
}
```

## Usage Instructions

### 1. Setup

1. Create a new Next.js project
2. Install the required dependencies
3. Copy the provided files to your project
4. Configure the API URL in `.env.local`

### 2. Development

```bash
# Start development server
npm run dev

# Build for production
npm run build

# Start production server
npm start
```

### 3. Testing

1. Ensure your Laravel API is running
2. Visit `http://localhost:3000/privacy-policy`
3. Test language switching
4. Verify content loads correctly

### 4. Deployment

1. Build the project: `npm run build`
2. Deploy to your hosting platform
3. Update API URL in environment variables
4. Test all functionality

## Features

- ✅ Multi-language support (English, Arabic, French)
- ✅ RTL support for Arabic
- ✅ Responsive design
- ✅ SEO optimization
- ✅ Error handling
- ✅ Loading states
- ✅ Language switching
- ✅ Static page rendering
- ✅ API integration
- ✅ TypeScript support (optional)

## Customization

### Adding New Languages

1. Add language to the `defaultTranslations` object in `lib/translations.js`
2. Update the language switcher component
3. Add language-specific styles if needed

### Adding New Pages

1. Create a new page component following the existing pattern
2. Add the route to your Next.js routing
3. Update navigation if needed

### Styling

1. Modify `styles/globals.css` for global styles
2. Add component-specific styles
3. Customize the design to match your brand

## Troubleshooting

### Common Issues

1. **API Connection Failed**: Check API URL and CORS settings
2. **Language Not Switching**: Verify localStorage is working
3. **Content Not Loading**: Check API response and error handling
4. **RTL Issues**: Verify RTL styles are applied correctly

### Debug Mode

Enable debug mode by adding console logs:

```javascript
// In api.js
api.interceptors.request.use(
  (config) => {
    console.log('API Request:', config);
    return config;
  }
);

api.interceptors.response.use(
  (response) => {
    console.log('API Response:', response);
    return response;
  }
);
```

This integration provides a complete solution for displaying static pages from your Laravel API in a Next.js website with full multi-language support.
