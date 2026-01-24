import api, {
  type TwoFactorSetupResponse,
  type TwoFactorConfirmResponse,
  type TwoFactorVerifyResponse,
} from "./api"

export const twoFactor = {
  /**
   * Get QR code data for 2FA setup
   */
  setup: async (): Promise<TwoFactorSetupResponse> => {
    const response = await api.post<TwoFactorSetupResponse>("/auth/2fa/setup")
    return response.data
  },

  /**
   * Confirm 2FA setup with verification code
   */
  confirm: async (
    code: string,
    secret: string
  ): Promise<TwoFactorConfirmResponse> => {
    const response = await api.post<TwoFactorConfirmResponse>(
      "/auth/2fa/confirm",
      {
        code,
        secret,
      }
    )
    return response.data
  },

  /**
   * Verify 2FA code during login
   */
  verify: async (
    tempToken: string,
    code: string
  ): Promise<TwoFactorVerifyResponse> => {
    const response = await api.post<TwoFactorVerifyResponse>(
      "/auth/2fa/verify",
      {
        code,
        temp_token: tempToken,
      }
    )
    return response.data
  },

  /**
   * Disable 2FA for the current user
   */
  disable: async (): Promise<{ message: string }> => {
    const response = await api.post<{ message: string }>("/auth/2fa/disable")
    return response.data
  },

  /**
   * Get recovery codes for the current user
   */
  getRecoveryCodes: async (): Promise<{ recovery_codes: string[] }> => {
    const response = await api.get<{ recovery_codes: string[] }>(
      "/auth/2fa/recovery-codes"
    )
    return response.data
  },
}
