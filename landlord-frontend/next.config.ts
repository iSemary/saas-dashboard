import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  output: 'export',
  images: { unoptimized: true },
  trailingSlash: true,
  allowedDevOrigins: ["landlord.saas.test", "127.0.0.1"],
};

export default nextConfig;
