import { useUserContext } from '../UserContext'
import React, { useEffect } from 'react'
import { useNavigate } from 'react-router-dom'

const Home= () => {
  const navigate=useNavigate()
  const {login,token,setToken,setUser}=useUserContext()
  useEffect(()=>{
    if(!token){
      navigate('/login')
    }
  },[token])
 
  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">Welcome to the Blog App</h1>
      <p>This is the home page of our authenticated blog application.</p>
    </div>
  )
}

export default Home

