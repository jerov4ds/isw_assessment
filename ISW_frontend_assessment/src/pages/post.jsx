import React, { useState, useEffect } from 'react';
import Spinner from '../components/Spinner';
import { useSelector, useDispatch } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { post, reset } from '../features/posts/postsSlice';

const Post = () => {
	const navigate = useNavigate();
	const dispatch = useDispatch();
	const [text, setText] = useState('');
	const [title, setTitle] = useState('');
	const [image, setImage] = useState('');
	const [apiError, setApiError] = useState('');
	const [errors, setErrors] = useState({});
	const { posts, isLoading, isError, isSuccess, message } = useSelector(
		(state) => state.post
	);

	useEffect(() => {
		if (isError) {
			setApiError(message);
			console.log(message);
		}
		if (isSuccess) {
			navigate('/admin');
		}
	}, [isError, isLoading, isSuccess, message, navigate, dispatch]);

	const validateForm = () => {
		const errors = {};
		if (!title) errors.title = 'Title is required';
		if (!text) errors.text = 'Text is required';
		if (!image) errors.image = 'File is required';

		setErrors(errors);
		return Object.keys(errors).length === 0;
	};

	const submitForm = (e) => {
		e.preventDefault();
		if (!validateForm()) return;

		const form = {
			title,
			text,
			image,
		};

		console.log(form);
		// dispatch(all_post(form));
	};

	return (
		<div
			className="container-fluid"
			style={{ margin: '80px auto', width: '700px' }}
		>
			<form className="form" onSubmit={submitForm}>
				<div className="mb-3 mt-3">
					<label htmlFor="email" className="form-label">
						Post Title:
					</label>
					<input
						type="text"
						className="form-control"
						name="title"
						value={title}
						onChange={(e) => setTitle(e.target.value)}
					/>
					{errors.title && <p className="error">{errors.title}</p>}
				</div>
				<div className="mb-3">
					<label htmlFor="pwd" className="form-label">
						Text:
					</label>
					<input
						type="text"
						className="form-control"
						value={text}
						onChange={(e) => setText(e.target.value)}
					/>
					{errors.text && <p className="error">{errors.text}</p>}
				</div>
				<div className="row m-0">
					<div className="mb-3 col-lg-12">
						<label htmlFor="address" className="form-label">
							File:
						</label>
						<input
							className="form-control textarea"
							placeholder="Enter address"
							type="file"
							value={image}
							onChange={(e) => setImage(e.target.value)}
						/>
						{errors.image && <p className="error">{errors.image}</p>}
					</div>
				</div>
				<div className="btn-div">
					<p className="error">
						{message && typeof message === 'object'
							? JSON.stringify(message)
							: message}
					</p>
					<button type="submit" className="btn btn-primary">
						{isLoading ? <Spinner /> : 'Post'}
					</button>
				</div>
			</form>
		</div>
	);
};

export default Post;
