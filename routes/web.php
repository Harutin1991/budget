<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix'=>'api/v1'], function() use($router){

    $router->get('/item/{allocationType}/{schoolId}/{pageType}', 'BudgetController@getBudgetItems');

    $router->get('/activity-totals/{allocationType}/{schoolId}', 'BudgetController@getTotalsForBarSection');
    $router->get('/activity-by-school-year/{allocationType}/{schoolYearId}', 'BudgetController@getAllocationBySchoolYear');
    $router->get('/activity-by-status/{allocationType}', 'BudgetController@getactivityByStatus');
    $router->get('/search-by-school/{allocationType}', 'BudgetController@searchBySchoolName');
    $router->get('/get-approval', 'BudgetController@getApprovals');
    $router->get('/get-schedule/{activityId}', 'BudgetController@getScheduleForCurrentActivity');
    $router->get('/get-atendee/{activityId}', 'BudgetController@getAttendies');
    $router->get('/get-atendee-types', 'BudgetController@getAttendyTypes');
    $router->get('/get-types-categories/{allocationId}', 'BudgetController@getCategories');
    $router->get('/get-types-subcategories/{allocationId}', 'BudgetController@getSubCategories');
    $router->get('/get-recurrance-types', 'BudgetController@getRecurranceTypes');
    $router->get('/search-attendy-participant/{schoolId}', 'BudgetController@searchAttendyParticipant');
    $router->get('/filter-budget/{allocationType}/{schoolId}/{pageType}', 'BudgetController@filterBudget');
    $router->get('/get-attendy-participiant/{summaryId}', 'BudgetController@getAttendyParticipiant');
    $router->get('/activity-show/{id}', 'BudgetController@show');
    $router->get('/get-item-status', 'BudgetController@getItemStatus');
    $router->get('/get-item-category-tracking', 'BudgetController@getCategoryTrackingList');
    

    $router->post('/item/{pageType}', 'BudgetController@create');
    $router->post('/add-attendy/{activityId}', 'BudgetController@addAttendy');
    $router->post('/add-schedule/{activityId}', 'BudgetController@addScheduleToActivity');
    $router->post('/add-participant/{schoolId}/{activityId}', 'BudgetController@addAttendyParticipant');
    $router->post('/add-participant-and-assign-to-activity/{schoolId}/{activityId}', 'BudgetController@addParticipantAndAssignToActivity');

    $router->put('/item/{id}', 'BudgetController@update');
    $router->put('/edit-schedule/{scheduleId}/{activityId}', 'BudgetController@editSchedule');
    $router->delete('/remove-item/{id}', 'BudgetController@destroy');
    $router->delete('/remove-attendy/{attendyId}', 'BudgetController@removeAttendy');
    $router->delete('/remove-schedule/{scheduleId}', 'BudgetController@removeScheduleFromActivity');
    $router->delete('/remove-attendy-parties/{participientId}/{activityId}', 'BudgetController@removeAttendyParties');

});
