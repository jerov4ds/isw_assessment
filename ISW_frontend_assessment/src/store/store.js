import { configureStore } from '@reduxjs/toolkit';
import authReducer from '../features/auth/authSlice';
import postReducer from '../features/posts/postsSlice';
export const store = configureStore({
	reducer: {
		auth: authReducer,
		all_post: postReducer,
	},
});
