export type Role = 'admin' | 'user';
export type LeaveStatus = 'pending' | 'approved' | 'rejected' | 'cancelled';

export interface User {
  id: number;
  name: string;
  email: string;
  role: Role;
}

export interface LeaveType {
  id: number;
  name: string;
  default_quota: number;
}

export interface LeaveBalance {
  id: number;
  user_id: number;
  leave_type_id: number;
  leave_type: LeaveType;
  year: number;
  balance: number;
}

export interface LeaveRequest {
  id: number;
  user_id: number;
  user?: User;
  leave_type_id: number;
  leave_type?: LeaveType;
  start_date: string;
  end_date: string;
  total_days: number;
  reason: string;
  status: LeaveStatus;
  comment?: string;
  responded_by?: number;
  responded_at?: string;
}