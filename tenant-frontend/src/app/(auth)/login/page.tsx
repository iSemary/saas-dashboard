'use client';

import { useCallback, useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { Loader2 } from 'lucide-react';
import { toast } from 'sonner';
import { useAuth } from '@/context/auth-context';
import { useI18n } from '@/context/i18n-context';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  TypingText,
  TypingTextCursor,
} from '@/components/animate-ui/primitives/texts/typing';
import { Fade } from '@/components/animate-ui/primitives/effects/fade';

export default function LoginPage() {
  const { login } = useAuth();
  const { t } = useI18n();
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const runLogin = useCallback(async () => {
    setError(null);
    setSubmitting(true);
    try {
      const result = await login({ email, password });
      if (result.requires2fa && result.tempToken) {
        window.localStorage.setItem('temp_token', result.tempToken);
        toast.success(
          t(
            'dashboard.auth.login_twofa_continue',
            'Enter your two-factor code to continue.',
          ),
        );
        router.push('/login/verify-2fa');
        return;
      }
      toast.success(
        t('dashboard.auth.login_signed_in', 'Signed in successfully.'),
      );
      router.replace('/dashboard');
    } catch {
      const msg = t(
        'dashboard.auth.invalid_credentials',
        'Invalid login credentials.',
      );
      setError(msg);
      toast.error(msg);
    } finally {
      setSubmitting(false);
    }
  }, [email, password, login, router, t]);

  return (
    <div className="flex flex-1 flex-col items-center justify-center px-4 py-10">
      <div className="mb-6 w-full max-w-lg text-center">
        <h1 className="text-2xl font-semibold tracking-tight">
          <TypingText
            text={t('dashboard.auth.welcome_back', 'Welcome back')}
            inView
            inViewOnce
            duration={50}
          />
          <TypingTextCursor />
        </h1>
        <Fade delay={300}>
          <p className="mt-1 text-sm text-muted-foreground">
            {t(
              'dashboard.auth.login_subtitle',
              'Sign in to manage your workspace.',
            )}
          </p>
        </Fade>
      </div>
      <Card className="w-full max-w-lg border-border/80 shadow-lg">
        <CardHeader>
          <CardTitle>{t('dashboard.auth.login_card_title', 'Login')}</CardTitle>
          <CardDescription>
            {t(
              'dashboard.auth.login_card_desc',
              'Use your account email and password.',
            )}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {error ? (
              <Alert variant="destructive">
                <AlertTitle>
                  {t('dashboard.auth.could_not_sign_in', 'Could not sign in')}
                </AlertTitle>
                <AlertDescription>{error}</AlertDescription>
              </Alert>
            ) : null}
            <div className="space-y-2">
              <Label htmlFor="email">
                {t('dashboard.auth.email', 'Email')}
              </Label>
              <Input
                id="email"
                type="email"
                autoComplete="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder={t(
                  'dashboard.auth.placeholder_email',
                  'you@example.com',
                )}
                disabled={submitting}
                onKeyDown={(e) => {
                  if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('password')?.focus();
                  }
                }}
              />
            </div>
            <div className="space-y-2">
              <div className="flex items-center justify-between gap-2">
                <Label htmlFor="password">
                  {t('dashboard.auth.password', 'Password')}
                </Label>
                <Link
                  href="/login/forgot-password"
                  className="text-xs font-medium text-primary hover:underline"
                >
                  {t('dashboard.auth.forgot_password_link', 'Forgot password?')}
                </Link>
              </div>
              <Input
                id="password"
                type="password"
                autoComplete="current-password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder={t(
                  'dashboard.auth.placeholder_password_dots',
                  '••••••••',
                )}
                disabled={submitting}
                onKeyDown={(e) => {
                  if (e.key === 'Enter' && !submitting) {
                    e.preventDefault();
                    void runLogin();
                  }
                }}
              />
            </div>
            <Button
              type="button"
              className="w-full"
              disabled={submitting}
              onClick={() => void runLogin()}
            >
              {submitting ? (
                <>
                  <Loader2 className="size-4 animate-spin" />
                  {t('dashboard.auth.signing_in', 'Signing in…')}
                </>
              ) : (
                t('dashboard.auth.sign_in', 'Sign in')
              )}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
