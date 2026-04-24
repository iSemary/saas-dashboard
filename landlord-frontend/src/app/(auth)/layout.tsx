import { LoginShell } from "@/components/login-shell";

export default function AuthLayout({ children }: { children: React.ReactNode }) {
  return <LoginShell>{children}</LoginShell>;
}
