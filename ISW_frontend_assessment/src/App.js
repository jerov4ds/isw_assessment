import {
	Route,
	createBrowserRouter,
	createRoutesFromElements,
	RouterProvider,
} from 'react-router-dom';
import SignUp from './pages/sign_up';
import SignIn from './pages/sign_in';
import Admin from './pages/admin';
import Post from './pages/post';

const App = () => {
	const router = createBrowserRouter(
		createRoutesFromElements(
			<>
				<Route path="/" element={<SignUp />}>
					{' '}
				</Route>
				<Route path="/sign_in" element={<SignIn />}></Route>
				<Route path="/admin" element={<Admin />}></Route>
				<Route path="/post" element={<Post />}></Route>
			</>
		)
	);
	return <RouterProvider router={router} />;
};

export default App;
