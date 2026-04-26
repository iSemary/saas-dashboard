import api from "@/lib/api";

const B = "/tenant/billing";

// Types
export interface BillingOverview {
  brand_id: number;
  brand_name: string;
  subscription: Subscription | null;
  active_modules: ModuleSubscription[];
  module_count: number;
  billing_profile: BillingProfile | null;
  upcoming_invoice: UpcomingInvoice | null;
  payment_status: string;
  balance: number;
  currency: string;
}

export interface Subscription {
  id: number;
  plan: Plan;
  status: string;
  price: number;
  currency: string;
  billing_cycle: string;
  trial_ends_at: string | null;
  current_period_start: string | null;
  current_period_end: string | null;
  next_billing_at: string | null;
  cancel_at_period_end: boolean;
}

export interface Plan {
  id: number;
  name: string;
  slug: string;
  description?: string;
  price: number;
  features?: PlanFeature[];
}

export interface PlanFeature {
  id: number;
  feature_key: string;
  feature_value: string;
  feature_type: string;
}

export interface ModuleSubscription {
  id: number;
  module_key: string;
  status: string;
  price: number | null;
  billing_cycle: string | null;
  current_period_end: string | null;
  next_billing_at: string | null;
}

export interface BillingProfile {
  id: number;
  default_gateway: string;
  has_payment_method: boolean;
  billing_email: string | null;
  tax_id: string | null;
  account_balance: number;
  auto_pay: boolean;
}

export interface UpcomingInvoice {
  subtotal: number;
  tax_amount: number;
  discount_amount: number;
  total_amount: number;
  line_items: InvoiceLineItem[];
  period_start: string;
  period_end: string;
  due_date: string;
  currency_code: string;
}

export interface InvoiceLineItem {
  line_type: string;
  description: string;
  quantity: number;
  unit_price: number;
  amount: number;
}

export interface ModuleCatalogItem {
  id: number;
  module_key: string;
  name: string;
  description: string | null;
  base_price: number | null;
  currency_code: string | null;
  billing_cycle: string;
  trial_days: number;
  is_subscribed: boolean;
  subscription: {
    id: number;
    status: string;
    price: number | null;
    current_period_end: string | null;
  } | null;
}

export interface Invoice {
  id: number;
  invoice_number: string;
  status: string;
  subtotal: number;
  tax_amount: number;
  discount_amount: number;
  total_amount: number;
  invoice_date: string;
  due_date: string;
  paid_at: string | null;
  items?: InvoiceItem[];
  currency?: { code: string; symbol: string };
}

export interface InvoiceItem {
  id: number;
  line_type: string;
  description: string;
  quantity: number;
  unit_price: number;
  amount: number;
  total_amount: number;
}

export interface Payment {
  id: number;
  payment_id: string;
  amount: number;
  gateway: string | null;
  status: string;
  gateway_payment_id: string | null;
  failure_message: string | null;
  paid_at: string | null;
  attempted_at: string;
  currency?: { code: string; symbol: string };
}

export interface PaymentMethod {
  id: number;
  gateway: string;
  type: string;
  last_four: string | null;
  brand: string | null;
  exp_month: number | null;
  exp_year: number | null;
  is_default: boolean;
}

export interface ProrationPreview {
  credit_amount: number;
  debit_amount: number;
  net_amount: number;
  days_remaining: number;
  days_in_period: number;
  proration_factor: number;
  description: string | null;
}

// API Functions

export async function getBillingOverview(): Promise<BillingOverview> {
  const res = await api.get<{ status: string; data: BillingOverview }>(`${B}/overview`);
  return res.data.data;
}

export async function getAvailablePlans(): Promise<Plan[]> {
  const res = await api.get<{ status: string; data: Plan[] }>(`${B}/plans`);
  return res.data.data;
}

export async function getAvailableModules(): Promise<ModuleCatalogItem[]> {
  const res = await api.get<{ status: string; data: ModuleCatalogItem[] }>(`${B}/modules`);
  return res.data.data;
}

export async function getInvoiceHistory(params?: { 
  status?: string; 
  from_date?: string; 
  to_date?: string;
  per_page?: number;
  page?: number;
}) {
  const res = await api.get(`${B}/invoices`, { params });
  return res.data;
}

export async function getInvoiceDetails(id: number): Promise<Invoice> {
  const res = await api.get<{ status: string; data: Invoice }>(`${B}/invoices/${id}`);
  return res.data.data;
}

export async function downloadInvoicePdf(id: number): Promise<Blob> {
  const res = await api.get(`${B}/invoices/${id}/download`, {
    responseType: "blob",
  });
  return res.data as Blob;
}

export async function payInvoice(id: number): Promise<{ status: string; data: Payment }> {
  const res = await api.post<{ status: string; data: Payment }>(`${B}/invoices/${id}/pay`);
  return res.data;
}

export async function getPaymentHistory(params?: {
  status?: string;
  gateway?: string;
  per_page?: number;
  page?: number;
}) {
  const res = await api.get(`${B}/payments`, { params });
  return res.data;
}

export async function retryPayment(id: number): Promise<{ status: string; data: Payment }> {
  const res = await api.post<{ status: string; data: Payment }>(`${B}/payments/${id}/retry`);
  return res.data;
}

export async function getPaymentMethods(): Promise<PaymentMethod[]> {
  const res = await api.get<{ status: string; data: PaymentMethod[] }>(`${B}/payment-methods`);
  return res.data.data;
}

export async function createSetupIntent(gateway: string): Promise<{ client_secret: string | null; customer_id: string | null }> {
  const res = await api.post<{ status: string; data: { client_secret: string | null; customer_id: string | null } }>(
    `${B}/payment-methods/setup-intent`,
    { gateway }
  );
  return res.data.data;
}

export async function attachPaymentMethod(
  gateway: string,
  paymentMethodId: string,
  setAsDefault: boolean = true
): Promise<PaymentMethod> {
  const res = await api.post<{ status: string; data: PaymentMethod }>(`${B}/payment-methods/attach`, {
    gateway,
    payment_method_id: paymentMethodId,
    set_as_default: setAsDefault,
  });
  return res.data.data;
}

export async function setDefaultPaymentMethod(id: number): Promise<PaymentMethod> {
  const res = await api.post<{ status: string; data: PaymentMethod }>(`${B}/payment-methods/${id}/default`);
  return res.data.data;
}

export async function removePaymentMethod(id: number): Promise<void> {
  await api.delete(`${B}/payment-methods/${id}`);
}

export async function subscribeToModule(
  moduleId: number,
  billingCycle: string = "monthly",
  immediate: boolean = true
): Promise<{ subscription: ModuleSubscription; proration: ProrationPreview | null; immediate_charge: number; invoice: Invoice | null }> {
  const res = await api.post<{ 
    status: string; 
    data: { 
      subscription: ModuleSubscription; 
      proration: ProrationPreview | null; 
      immediate_charge: number; 
      invoice: Invoice | null;
    } 
  }>(`${B}/modules/subscribe`, {
    module_id: moduleId,
    billing_cycle: billingCycle,
    immediate,
  });
  return res.data.data;
}

export async function unsubscribeFromModule(
  moduleId: number,
  immediate: boolean = false,
  refund: boolean = false,
  reason?: string
): Promise<{ subscription: ModuleSubscription; proration: ProrationPreview | null; refund_amount: number; immediate: boolean }> {
  const res = await api.post<{
    status: string;
    data: {
      subscription: ModuleSubscription;
      proration: ProrationPreview | null;
      refund_amount: number;
      immediate: boolean;
    };
  }>(`${B}/modules/unsubscribe`, {
    module_id: moduleId,
    immediate,
    refund,
    reason,
  });
  return res.data.data;
}

export async function previewModuleProration(
  moduleId: number,
  billingCycle: string
): Promise<ProrationPreview | null> {
  const res = await api.get<{ status: string; data: ProrationPreview | null }>(`${B}/modules/proration-preview`, {
    params: { module_id: moduleId, billing_cycle: billingCycle },
  });
  return res.data.data;
}

export async function getUpcomingInvoice(): Promise<UpcomingInvoice | null> {
  const res = await api.get<{ status: string; data: UpcomingInvoice | null }>(`${B}/upcoming-invoice`);
  return res.data.data;
}

export async function subscribeToPlan(
  planId: number,
  gateway: string,
  currencyId: number,
  billingCycle: string = "monthly",
  successUrl?: string,
  cancelUrl?: string
): Promise<{ checkout_url: string | null }> {
  const res = await api.post<{ status: string; data: { checkout_url: string | null } }>(`${B}/plans/subscribe`, {
    plan_id: planId,
    gateway,
    currency_id: currencyId,
    billing_cycle: billingCycle,
    success_url: successUrl,
    cancel_url: cancelUrl,
  });
  return res.data.data;
}

export async function changePlan(
  newPlanId: number,
  immediate: boolean = false,
  prorate: boolean = true
): Promise<{ success: boolean }> {
  const res = await api.post<{ status: string; data: { success: boolean } }>(`${B}/plans/change`, {
    new_plan_id: newPlanId,
    immediate,
    prorate,
  });
  return res.data.data;
}

export async function cancelPlan(atPeriodEnd: boolean = true, reason?: string): Promise<{ success: boolean }> {
  const res = await api.post<{ status: string; data: { success: boolean } }>(`${B}/plans/cancel`, {
    at_period_end: atPeriodEnd,
    reason,
  });
  return res.data.data;
}
