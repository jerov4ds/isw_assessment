# isw_assessment
Interswitch Assessment for software engineer

The backend assessment is done in the ISW_backend_assessment directory

### Backend Assessment

#### Assessment questions:
1. Design a RESTful API with endpoints for user registration, login, posting updates, and
   retrieving updates.
2. Implement authentication using JWT tokens.
3. Create a CRUD operation for users and posts.
4. Optimize database queries for efficient data retrieval.
5. Implement rate limiting to prevent abuse.
6. Design a database schema to store user information, posts, and comments.
7. Implement caching to improve performance.
8. Develop a robust error handling system to log and manage errors.
9. Write unit Test for each endpoint provided above.

This assessment was done using PHP/Laravel. I decided to use Laravel since no preferred language was specified and Laravel provides an excellent boiler-plate for all the tasks above, considering the short time constraints 

1. User registration, login  in the ISW_backend_assessment/app/Http/Controllers/AuthController.php
2. CRUD for Posts is found ISW_backend_assessment/app/Http/Controllers/PostController.php
3. Database schemas are found in ISW_backend_assessment/database/migrations
4. Postman collection for all endpoints are found in https://documenter.getpostman.com/view/20934151/2sA3e1BVjM
5. Rate limiting is done in the route file, using the default laravel rate limiting ISW_backend_assessment/routes/api.php
6. Unit tests are found in ISW_backend_assessment/tests/Unit

### Instructions to Running backend application
1. Ensure PHP and Composer are in stalled in your machine
2. Clone repo using `git clone https://github.com/jerov4ds/isw_assessment.git`
3. run `composer install`
4. CD to your repo directory
5. rename '.env.example' to '.env'
6. replace db configuration with your mysql db configuration 
7. run `php artisan migrate`
8. run `php artisan db-seed`
9. run `php artisan migrate`
10. Your application should be up and running, get the endpoints from the postman collection above and make requests
