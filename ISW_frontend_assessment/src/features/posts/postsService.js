import axios from 'axios';
const API_URL =
	'https://ca84-102-215-57-26.ngrok-free.app/api/v1/posts?page_size=20&page_num=1&sort_by=created_at&sort_type=DESC&title&user&from&to';

const fetch_all_post = async (token) => {
	const config = {
		headers: {
			Authorization: `Bearer ${token}`,
		},
	};
	const response = await axios.get(API_URL, config);
	return response.data.data;
};
const dashboardService = {
	fetch_all_post,
};
export default dashboardService;
