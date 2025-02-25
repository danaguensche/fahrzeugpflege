import axios from 'axios';

const API_BASE_URL = 'https://localhost:8000/api/';
const TOKEN_KEY = 'token';

const axiosInstance = axios.create({
    baseURL: API_BASE_URL,
    timeout: 10000, 
    headers: {
        'Content-Type': 'application/json',
    },
});

// Request Interceptor
axiosInstance.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem(TOKEN_KEY);
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Response Interceptor
axiosInstance.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    console.log('Nicht autorisiert. Bitte erneut anmelden.');
                    $router.push({ name: 'login' });
                    break;
                case 404:
                    console.log('Ressource nicht gefunden');
                    break;
                case 500:
                    console.log('Serverfehler');
                    break;
                default:
                    console.log('Ein Fehler ist aufgetreten');
            }
        } else if (error.request) {
            console.log('Keine Antwort vom Server erhalten');
        } else {
            console.log('Fehler beim Einrichten der Anfrage', error.message);
        }
        return Promise.reject(error);
    }
);

export default axiosInstance;
