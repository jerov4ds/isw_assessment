import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useSelector, useDispatch } from 'react-redux';
import { logout, reset } from '../features/auth/authSlice';
import { all_post } from '../features/posts/postsSlice';
const Admin = () => {
	const navigate = useNavigate();
	const dispatch = useDispatch();
	const [user, setUser] = useState(null); // Initialize as null
	const { posts, isLoading, isError, isSuccess, message } = useSelector(
		(state) => state.all_post
	);
	useEffect(() => {
		const storedUser = JSON.parse(localStorage.getItem('user'));
		if (storedUser) {
			setUser(storedUser);
		}
	}, []);
	console.log(posts);
	// if (!user) {
	// 	return <div>Welcome Guest</div>; // Display a loading state while user data is being fetched
	// }
	const onLongout = () => {
		dispatch(logout());
		// dispatch(reset());
		navigate('/sign_in');
	};
	return (
		<div>
			<nav className="navbar navbar-expand-sm navbar-dark bg-dark p-3">
				<div className="container-fluid">
					<a className="navbar-brand" href="#">
						<img
							src="https://images.unsplash.com/photo-1633332755192-727a05c4013d?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MzV8fGF2YXRhcnN8ZW58MHx8MHx8fDA%3D"
							alt="Avatar Logo"
							style={{ width: '40px' }}
							className="rounded-pill"
						/>
					</a>
					<button
						className="navbar-toggler"
						type="button"
						data-bs-toggle="collapse"
						data-bs-target="#mynavbar"
					>
						<span className="navbar-toggler-icon"></span>
					</button>
					<div className="collapse navbar-collapse" id="mynavbar">
						<ul className="navbar-nav me-auto">
							<li className="nav-item" onClick={onLongout}>
								<a className="nav-link" href="#">
									Log out
								</a>
							</li>
							<li className="nav-item">
								<a className="nav-link" href="#">
									Profile
								</a>
							</li>
						</ul>
						<form className="d-flex">
							<input
								className="form-control me-2"
								type="text"
								placeholder="Search"
							/>
							<button
								className="btn btn-primary"
								style={{ width: '200px' }}
								type="button"
							>
								Search
							</button>
						</form>
					</div>
				</div>
			</nav>

			<div className="container mt-3">
				<h2 className="text-white">
					Welcome {user ? <h2>{user.user.name}</h2> : <h2>Guest</h2>}
				</h2>
				<table className="table table-hover">
					<thead className="p-5">
						<tr>
							<th>Firstname</th>
							<th>Lastname</th>
							<th>Email</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>John</td>
							<td>Doe</td>
							<td>john@example.com</td>
						</tr>
						<tr>
							<td>Mary</td>
							<td>Moe</td>
							<td>mary@example.com</td>
						</tr>
						<tr>
							<td>July</td>
							<td>Dooley</td>
							<td>july@example.com</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	);
};

export default Admin;
