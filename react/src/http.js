import axios from 'axios'
const axiosClient = axios.create({
    baseURL:import.meta.env.VITE_BACK_END_API,
    withCredentials: true,

})
axiosClient.interceptors.request.use(function (config) {
    const token = window.localStorage.getItem('token')
    if (token) {
        config.headers.Authorization = 'Bearer ' + token
    }
    return config
})
axiosClient.interceptors.response.use((response) => {
    return response;
}, (error) => {
    if (error.response && error.response.status === 401) {
        window.localStorage.removeItem('token')
        window.location.reload()
        return error
    }
    throw error
});
export default axiosClient
