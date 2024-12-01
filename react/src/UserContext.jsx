import React, { createContext, useContext, useEffect, useState } from 'react'
import axiosClient from './http'
const StateContext = createContext({
  message: {},
  login: (data) => { },
  logout: () => { },
  setToken: (token) => { },
  getUser: () => { },
  setUser: () => { },
})
export default function UserContext({ children }) {
  const [user, setUser] = useState({})   
  const [token, _setToken] = useState(window.localStorage.getItem('token') || '')
  const csrf= async () => {
    return await axiosClient.get('/sanctum/csrf-cookie', {
        baseURL: import.meta.env.VITE_BASE_URL_BACK_END_API
    })
  }
  const login = async (data) => {
    await csrf()
    return await axiosClient.post('/login', data)
}
  const setToken = (token) => {
    if (token) {
      window.localStorage.setItem('token', token)
    } else {
      window.localStorage.removeItem('token', token)
    }
    _setToken(token)
  }

  const getUser = async () => {
    return await axiosClient.get('/user')
  }
  const logout = async () => {
    return await axiosClient.post('/logout')
  }
useEffect(()=>{
  if(token){
    getUser().then((data)=>setUser(data.data.user))
  }
},[token])
  return (
    <StateContext.Provider value={{
      token,user,
      login,
      setToken,setUser,
      getUser,logout,
    }}>
      {children}
    </StateContext.Provider>
  )
}
export const useUserContext = () => useContext(StateContext)
