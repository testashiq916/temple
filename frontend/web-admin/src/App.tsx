import { Navigate, Route, Routes } from 'react-router-dom'
import { AuthProvider } from './lib/auth'
import { ProtectedRoute } from './components/ProtectedRoute'
import { Layout } from './components/Layout'
import { Login } from './pages/Login'
import { Dashboard } from './pages/Dashboard'
import { Temples } from './pages/Temples'
import { Devotees } from './pages/Devotees'
import { SevaBookings } from './pages/SevaBookings'
import { Donations } from './pages/Donations'
import { Accounting } from './pages/Accounting'

export default function App() {
  return (
    <AuthProvider>
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route
          element={
            <ProtectedRoute>
              <Layout />
            </ProtectedRoute>
          }
        >
          <Route path="/" element={<Dashboard />} />
          <Route path="/temples" element={<Temples />} />
          <Route path="/devotees" element={<Devotees />} />
          <Route path="/sevas" element={<SevaBookings />} />
          <Route path="/donations" element={<Donations />} />
          <Route path="/accounting" element={<Accounting />} />
        </Route>
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </AuthProvider>
  )
}
