import axios from 'axios';
const API_URL = 'https://ca84-102-215-57-26.ngrok-free.app/api/v1/';
// REGISTER USER
const register = async (userData) => {
	const response = await axios.post(API_URL + 'register', userData);
	if (response.data) {
		localStorage.setItem('user', JSON.stringify(response.data.data));
	}
	return response.data;
};

// login USER
const login = async (userData) => {
	const response = await axios.post(API_URL + 'login', userData);
	if (response.data) {
		localStorage.setItem('user', JSON.stringify(response.data.data));
	}
	return response.data;
};

// logout
const logout = () => {
	localStorage.removeItem('user');
};
const authService = {
	register,
	login,
	logout,
};
export default authService;
