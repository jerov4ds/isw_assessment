import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useSelector, useDispatch } from 'react-redux';
import Spinner from '../components/Spinner';
import { register, reset } from '../features/auth/authSlice';
import '../index.css';

const SignUp = () => {
	const navigate = useNavigate();
	const dispatch = useDispatch();
	const [name, setName] = useState('');
	const [email, setEmail] = useState('');
	const [plan, setPlan] = useState('');
	const [country, setCountry] = useState('');
	const [state, setState] = useState('');
	const [address, setAddress] = useState('');
	const [website, setWebsite] = useState('');
	// const [company_name, setCompany_name] = useState('');
	const [mobile, setMobile] = useState('');
	const [password, setPassword] = useState('');
	const [apiError, setApiError] = useState('');
	const [password_confirmation, setPassword_confirmation] = useState('');
	const [errors, setErrors] = useState({});
	const { user, isLoading, isError, isSuccess, message } = useSelector(
		(state) => state.auth
	);

	useEffect(() => {
		if (isError) {
			setApiError(message);
			console.log(message);
		}
		if (isSuccess) {
			navigate('/sign_in');
		}
	}, [user, isError, isLoading, isSuccess, message, navigate, dispatch]);

	const validateForm = () => {
		const errors = {};
		if (!name) errors.name = 'Name is required';
		if (!email) errors.email = 'Email is required';
		else if (!/\S+@\S+\.\S+/.test(email)) errors.email = 'Email is invalid';
		if (!password) errors.password = 'Password is required';
		if (password !== password_confirmation)
			errors.password_confirmation = 'Passwords do not match';
		if (!password_confirmation)
			errors.password_confirmation = 'Confirm Password is required';
		if (!plan) errors.plan = 'Plan is required';
		if (!country) errors.country = 'Country is required';
		// if (!company_name) errors.company_name = 'Company is required';
		if (!website) errors.website = 'Website is required';
		if (!state) errors.state = 'State is required';
		if (!address) errors.address = 'Address is required';
		if (!mobile) errors.mobile = 'Mobile is required';
		if (!/\d+/.test(mobile)) errors.mobile = 'Mobile must be a number';

		setErrors(errors);
		return Object.keys(errors).length === 0;
	};

	const submitForm = (e) => {
		e.preventDefault();
		if (!validateForm()) return;

		const form = {
			name,
			email,
			mobile,
			password,
			password_confirmation,
			// company_name,
			country,
			website,
			state,
			address,
			plan,
		};

		console.log(form);
		// Dispatch your signup action here
		dispatch(register(form));
	};

	return (
		<React.Fragment>
			<div className="cont">
				<h1>Registration</h1>
				<form className="form" onSubmit={submitForm}>
					<div className="row">
						<div className="col-lg-6">
							<div className="mb-3">
								<label htmlFor="email" className="form-label">
									Email:
								</label>
								<input
									type="email"
									className="form-control"
									placeholder="Enter email"
									name="email"
									value={email}
									onChange={(e) => setEmail(e.target.value)}
								/>
								{errors.email && <p className="error">{errors.email}</p>}
							</div>
							<div className="mb-3">
								<label htmlFor="name" className="form-label">
									Name:
								</label>
								<input
									type="text"
									className="form-control"
									placeholder="Enter name"
									value={name}
									onChange={(e) => setName(e.target.value)}
								/>
								{errors.name && <p className="error">{errors.name}</p>}
							</div>
							<div className="mb-3">
								<label htmlFor="pwd" className="form-label">
									Password:
								</label>
								<input
									type="password"
									className="form-control"
									placeholder="Enter password"
									value={password}
									onChange={(e) => setPassword(e.target.value)}
								/>
								{errors.password && <p className="error">{errors.password}</p>}
							</div>
							<div className="mb-3">
								<label htmlFor="pwd-confirm" className="form-label">
									Confirm Password:
								</label>
								<input
									type="password"
									className="form-control"
									placeholder="Enter password"
									value={password_confirmation}
									onChange={(e) => setPassword_confirmation(e.target.value)}
								/>
								{errors.password_confirmation && (
									<p className="error">{errors.password_confirmation}</p>
								)}
							</div>
							{/*<div className="mb-3">*/}
							{/*	<label htmlFor="plan" className="form-label">*/}
							{/*		Plan:*/}
							{/*	</label>*/}
							{/*	<input*/}
							{/*		type="text"*/}
							{/*		className="form-control"*/}
							{/*		placeholder="Enter plan"*/}
							{/*		value={plan}*/}
							{/*		onChange={(e) => setPlan(e.target.value)}*/}
							{/*	/>*/}
							{/*	{errors.plan && <p className="error">{errors.plan}</p>}*/}
							{/*</div>*/}
						</div>
						<div className="col-lg-6">
							<div className="mb-3">
								<label htmlFor="country" className="form-label">
									Country:
								</label>
								<input
									type="text"
									className="form-control"
									placeholder="Enter country"
									value={country}
									onChange={(e) => setCountry(e.target.value)}
								/>
								{errors.country && <p className="error">{errors.country}</p>}
							</div>
							<div className="mb-3">
								<label htmlFor="state" className="form-label">
									State:
								</label>
								<input
									type="text"
									className="form-control"
									placeholder="Enter state"
									value={state}
									onChange={(e) => setState(e.target.value)}
								/>
								{errors.state && <p className="error">{errors.state}</p>}
							</div>

							<div className="mb-3">
								<label htmlFor="website" className="form-label">
									Website:
								</label>
								<input
									type="text"
									className="form-control"
									placeholder="Enter website"
									value={website}
									onChange={(e) => setWebsite(e.target.value)}
								/>
								{errors.website && <p className="error">{errors.website}</p>}
							</div>
							<div className="mb-3">
								<label htmlFor="mobile" className="form-label">
									Phone Number:
								</label>
								<input
									type="text"
									className="form-control"
									placeholder="Enter mobile"
									value={mobile}
									onChange={(e) => setMobile(e.target.value)}
								/>
								{errors.mobile && <p className="error">{errors.mobile}</p>}
							</div>
						</div>
						<div className="row">
							<div className="mb-3 col-lg-12">
								<label htmlFor="address" className="form-label">
									Address:
								</label>
								<textarea
									className="form-control textarea"
									placeholder="Enter address"
									value={address}
									onChange={(e) => setAddress(e.target.value)}
								></textarea>
								{errors.address && <p className="error">{errors.address}</p>}
							</div>
						</div>
					</div>
					<div className="btn-div">
						<p className="error">
							{message && typeof message === 'object'
								? JSON.stringify(message)
								: message}
						</p>
						<button type="submit" className="btn btn-primary">
							{isLoading ? <Spinner /> : 'Sign Up'}
						</button>
						<br />
						<small>
							Already have an account? <a href="sign_in">sign in</a>
						</small>
					</div>
				</form>
			</div>
		</React.Fragment>
	);
};

export default SignUp;
