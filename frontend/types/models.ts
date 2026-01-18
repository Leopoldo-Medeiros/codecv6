// Core domain types for the application

export interface User {
  id: number
  fullname: string
  email: string
  role?: string
  consultant_id?: number | null
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

export interface AuthState {
  user: User | null
  isAuthenticated: boolean
  isAdmin: boolean
  isClient: boolean
  isConsultant: boolean
}
