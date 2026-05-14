// Core domain types for the application

export type UserRole = 'admin' | 'client' | 'consultant'

export interface User {
  id: number
  fullname: string
  email: string
  role?: UserRole
  consultant_id?: number | null
  consultant?: User | null
  needs_onboarding?: boolean
  profile?: Profile
  created_at?: string
  updated_at?: string
}

export interface Profile {
  id?: number
  user_id?: number
  birth_date?: string
  profession?: string
  profile_image?: string
  profile_image_url?: string
  website?: string
  github?: string
  linkedin?: string
  instagram?: string
  facebook?: string
  // Onboarding / career fields (added in 2026_04_19 migrations).
  goal?: string
  level?: string
  stack?: string[]
  product_interest?: string
  availability_hours?: number
  timeline?: string
}

export interface Course {
  id: number
  name: string
  slug: string
  description?: string
  user_id: number
  user?: User
  created_at?: string
  updated_at?: string
}

export interface Plan {
  id: number
  name: string
  description?: string
  price?: number
  consultant_id: number
  consultant?: User
  clients?: User[]
  paths?: Path[]
  created_at?: string
  updated_at?: string
}

export interface Path {
  id: number
  name: string
  description?: string
  consultant_id: number
  consultant?: User
  plans?: Plan[]
  created_at?: string
  updated_at?: string
}

export interface Job {
  id: number
  title: string
  description?: string
  company: string
  location?: string
  salary?: string
  consultant_id: number
  consultant?: User
  created_at?: string
  updated_at?: string
}

export interface Role {
  id: number
  name: string
}

// API Response types
export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from?: number
  to?: number
}

export interface PaginatedResponse<T> {
  data: T[]
  meta?: PaginationMeta
  links?: {
    first?: string
    last?: string
    prev?: string | null
    next?: string | null
  }
}

export interface ApiResponse<T> {
  data: T
  message?: string
}

// Auth types
export interface LoginCredentials {
  email: string
  password: string
}

export interface LoginResponse {
  user: User
  access_token: string
  token_type: string
}

export interface Challenge {
  id: number
  title: string
  slug: string
  description: string
  difficulty: 'beginner' | 'intermediate' | 'advanced'
  boilerplate_code: string
  tests_code: string
  is_premium: boolean
  price_eur?: number | null
  created_by: number
  creator?: User
  created_at?: string
  updated_at?: string
}

export interface AuthState {
  user: User | null
  isAuthenticated: boolean
  isAdmin: boolean
  isClient: boolean
  isConsultant: boolean
}
