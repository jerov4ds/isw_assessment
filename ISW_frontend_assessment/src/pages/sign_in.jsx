import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useSelector, useDispatch } from 'react-redux';
import Spinner from '../components/Spinner';
import { login, reset } from '../features/auth/authSlice';
const Sign_in = () => {
	const navigate = useNavigate();
	const dispatch = useDispatch();
	const [email, setEmail] = useState('');
	const [password, setPassword] = useState('');
	const [apiError, setApiError] = useState('');
	const [errors, setErrors] = useState({});
	const { user, isLoading, isError, isSuccess, message } = useSelector(
		(state) => state.auth
	);
	useEffect(() => {
		if (isError) {
			setApiError(message);
		}
		if (isSuccess) {
			navigate('/admin');
		}
	}, [user, isError, isLoading, isSuccess, message, navigate, dispatch]);

	const validateForm = () => {
		const errors = {};
		if (!email) errors.email = 'Email is required';
		else if (!/\S+@\S+\.\S+/.test(email)) errors.email = 'Email is invalid';
		if (!password) errors.password = 'Password is required';

		setErrors(errors);
		return Object.keys(errors).length === 0;
	};

	const submitForm = (e) => {
		e.preventDefault();
		if (!validateForm()) return;

		const form = {
			email,
			password,
		};

		console.log(form);
		// Dispatch your signup action here
		dispatch(login(form));
	};
	return (
		<React.Fragment>
			{/* <div className="row m-5"> */}
			<div className="cont">
				<h1>Login</h1>
				<form className="form" onSubmit={submitForm}>
					<div class="mb-3 mt-3">
						<label for="email" class="form-label">
							Email:
						</label>
						<input
							type="email"
							class="form-control"
							name="email"
							value={email}
							onChange={(e) => setEmail(e.target.value)}
						/>
						{errors.email && <p className="error">{errors.email}</p>}
					</div>
					<div class="mb-3">
						<label for="pwd" class="form-label">
							Password:
						</label>
						<input
							type="password"
							class="form-control"
							value={password}
							onChange={(e) => setPassword(e.target.value)}
						/>
						{errors.password && <p className="error">{errors.password}</p>}
					</div>
					<div className="btn-div">
						<p className="error">
							{message && typeof message === 'object'
								? JSON.stringify(message)
								: message}
						</p>
						<button type="submit" class="btn btn-primary">
							{isLoading ? <Spinner /> : 'Sign in'}
						</button>
						<small>
							Don't have an account ? <a href="/">sign up</a>
						</small>
					</div>
				</form>
			</div>
			{/* </div> */}
		</React.Fragment>
	);
};
export default Sign_in;
