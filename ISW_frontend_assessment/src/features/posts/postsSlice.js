import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import postService from './postsService';

const initialState = {
	posts: [],
	isError: false,
	isSuccess: false,
	isLoading: false,
	message: '',
};
// // get dashboard
export const all_post = createAsyncThunk(
	'posts/all_post',
	async (_, thunkAPI) => {
		try {
			const token = thunkAPI.getState().auth.user.user.access_token;
			return await postService.all_post(token);
		} catch (error) {
			const message =
				(error.response &&
					error.response.data &&
					error.response.data.message) ||
				error.message ||
				error.stringfy();
			return thunkAPI.rejectWithValue(message);
		}
	}
);
export const postsSlice = createSlice({
	name: 'post',
	initialState,
	reducers: {
		reset: (state) => {
			state.isError = false;
			state.isSuccess = false;
			state.isLoading = false;
			state.message = '';
		},
	},
	extraReducers: (builder) => {
		builder
			.addCase(all_post.pending, (state) => {
				state.isLoading = true;
			})
			.addCase(all_post.fulfilled, (state, action) => {
				state.isLoading = false;
				state.isSuccess = true;
				state.posts = action.payload;
			})
			.addCase(all_post.rejected, (state, action) => {
				state.isLoading = false;
				state.isError = true;
				state.message = action.payload;
			});
	},
});
export const { reset } = postsSlice.actions;
export default postsSlice.reducer;
