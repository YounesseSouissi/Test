import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import App from './App.jsx'
import './index.css'
import UserContext from './UserContext.jsx'
import { ThemeProvider } from './providers/theme-provider.jsx'
createRoot(document.getElementById('root')).render(
  <StrictMode>
    <UserContext>
      <ThemeProvider>
      <App />
      </ThemeProvider>
    </UserContext>
  </StrictMode>,
)
