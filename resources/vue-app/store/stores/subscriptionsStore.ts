import { ref } from "vue";
import { defineStore } from "pinia";

// Types
type SubscriptionPlan = 'Basic' | 'Premium' | 'Deluxe';

type PaymentMethod = 'Credit Card' | 'PayPal' | 'Bank Transfer';

type SubscriptionStatus = 'Active' | 'Expired';

interface UserSubscription {
  userId: string;
  subscriptionPlan: SubscriptionPlan;
  startDate: string;
  endDate: string;
  status: SubscriptionStatus;
  paymentMethod: PaymentMethod;
  country: string;
  planDuration: string;
  monthlyFee: number;
  totalPaid: number;
}

export const useSubscriptionsStore = defineStore('subscriptionsStore', () => {

    const subscriptions = ref<UserSubscription[]>([
        {
          userId: 'U001',
          subscriptionPlan: 'Basic',
          startDate: '2025-01-01',
          endDate: '2025-06-01',
          status: 'Active',
          paymentMethod: 'Credit Card',
          country: 'Egypt',
          planDuration: '6 months',
          monthlyFee: 5,
          totalPaid: 30
        },
        {
          userId: 'U002',
          subscriptionPlan: 'Premium',
          startDate: '2025-02-15',
          endDate: '2025-08-15',
          status: 'Active',
          paymentMethod: 'PayPal',
          country: 'Egypt',
          planDuration: '6 months',
          monthlyFee: 10,
          totalPaid: 60
        }
      ]);

    return { subscriptions };

});