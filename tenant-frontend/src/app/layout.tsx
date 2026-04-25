import type { Metadata } from "next";
import { Geist, Geist_Mono } from "next/font/google";
import "./globals.css";
import { ThemeProvider } from "next-themes";
import { AuthProvider } from "@/context/auth-context";
import { I18nProvider } from "@/context/i18n-context";
import { FeatureFlagProvider } from "@/context/feature-flag-context";
import { TooltipProvider } from "@/components/ui/tooltip";
import { AppToaster } from "@/components/app-toaster";
import { AccentProvider } from "@/context/accent-context";
import { AnimationProvider } from "@/context/animation-context";
import { ModuleProvider } from "@/context/module-context";
import { APP_DESCRIPTION, APP_TITLE } from "@/lib/app-config";

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
});

export const metadata: Metadata = {
  title: APP_TITLE,
  description: APP_DESCRIPTION,
  icons: {
    icon: [{ url: "/assets/favicon.svg", type: "image/svg+xml" }],
    shortcut: "/assets/favicon.svg",
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html
      lang="en"
      suppressHydrationWarning
      className={`${geistSans.variable} ${geistMono.variable} h-full antialiased`}
    >
      <body className="min-h-full flex flex-col" suppressHydrationWarning>
        <ThemeProvider attribute="class" defaultTheme="dark" enableSystem>
          <TooltipProvider delay={0}>
            <I18nProvider>
              <AuthProvider>
                <ModuleProvider>
                  <AccentProvider>
                    <AnimationProvider>
                      <FeatureFlagProvider>{children}</FeatureFlagProvider>
                    </AnimationProvider>
                  </AccentProvider>
                </ModuleProvider>
              </AuthProvider>
              <AppToaster />
            </I18nProvider>
          </TooltipProvider>
        </ThemeProvider>
      </body>
    </html>
  );
}
