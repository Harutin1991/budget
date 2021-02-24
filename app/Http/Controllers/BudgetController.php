<?php

namespace App\Http\Controllers;
use App\Budget;
use App\ActivityAttendeeSummary;
use App\ActivitySchoolAttendeeList;
use App\CategoryType;
use App\Schedule;
use App\ScheduleRecurrance;
use App\ScheduleRecurranceType;
use App\School;
use App\SchoolYear;
use App\ApprovalType;
use App\ApprovalStatus;
use App\ActivityAttendee;
use App\ActivityAttendeeType;
use App\BudgetItemDetails;
use App\Allocations;
use App\Category;
use App\ActivitySchedule;
use Illuminate\Http\Request;
use App\Http;
use App\Helper\ResponseHelper;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Campus;
use App\BudgetItemStatus;
use App\BillingStatus;
use App\CategoryTracking;


class BudgetController extends Controller
{
    private $limit = 10;
    private $page = 1;
    private static $currentYearId = NULL;
    private $condition = ['param' => null, 'value' => null];
    private $schoolAllocation = null;
    private $schoolId = null;
    private static $allocationType = 1;

    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        if(!self::$currentYearId) {
            $currentYear = SchoolYear::where('is_current',1)->first();
            self::$currentYearId = $currentYear->id;
        }

    }

    public function getBudgetItems($allocationType, $schoolId, $pageType, Request $request)
    {
        $success = true;
        $errorMessage = '';
        $itemsResponse = [];
        $pagesCount = 0;
        $isFinal = false;
        try{
            $limit = $request->get('limit') ? $request->get('limit') : $this->limit;
            $page = $request->get('page') ? $request->get('page') : $this->page;
            $skip = (!$page) ? 0 : ($page - 1) * $limit;

            $allocation = Allocations::where('allocation_type_id' , $allocationType)->where('school_id',$schoolId)->first();
            if($allocation) {
              $isFinal =  (int)$allocation->is_final;
            }

            $budgetItems = Budget::with('category','subCategory','school','supplier','status','approvalStatus','approvalTypes','details','campus','traking','billing')->
                    where('allocation_type_id', $allocationType)->where('item_type_id',$pageType)->where('school_id', $schoolId)
                    ->skip($skip)->take($limit)->orderBy('start_date', 'DESC')->get();
            $itemsResponse = ResponseHelper::makeBudgetData($budgetItems);
            $activityCount = Budget::where('allocation_type_id', $allocationType)->where('item_type_id',1)->where('school_id', $schoolId)->count();
            $pagesCount = ceil($activityCount / $limit);
        } catch (Throwable $e){
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['items' => $itemsResponse, 'pagesCount' => $pagesCount,'isSchoolAllocationFinal' => $isFinal, 'success'=>$success, 'errorMessage'=>$errorMessage]);
    }


    public function filterBudget($allocationType, $schoolId,$pageType, Request $request)
    {
        $searchWord = $request->get('search') ? $request->get('search') : NULL;
        $activityType = $request->get('type') ? $request->get('type') : NULL;
        $schoolYear = $request->get('year') ? $request->get('year') : NULL;

        $limit = $request->get('limit') ? $request->get('limit') : $this->limit;
        $page = $request->get('page') ? $request->get('page') : $this->page;
        $skip = (!$page) ? 0 : ($page - 1) * $limit;

        $schoolYearId = (int)$schoolYear;
        //$activityType = (bool)$activityType;

        $query = Budget::query();
        $query->with('category','subCategory','school','supplier','status','approvalStatus','approvalTypes','details','campus','traking','billing');
        $query->where('allocation_type_id', $allocationType);
        $query->where('school_id', $schoolId);
        $query->where('item_type_id', $pageType);

        $schoolIds = [];
        if ($searchWord) {
            $query->where('name', 'like', '%' . $searchWord . '%')->get();
        }
        if ($activityType) {
            $query->where('allocation_type_id', $activityType);
        }
        if ($schoolYearId) {
            $query->where('school_year_id', $schoolYearId);
        }
        $activityCount = $query->count();
        $activity = $query->skip($skip)->take($limit)->get();
        $activityResponse = ResponseHelper::makeBudgetData($activity);
        $pagesCount = ceil($activityCount / $limit);
        return response()->json(['items' => $activityResponse, 'pagesCount' => $pagesCount]);
    }

    public function getTotalsForBarSection($allocationType, $schoolId, Request $request)
    {
        $allocation = Allocations::where('allocation_type_id', $allocationType)->where('school_year_id', self::$currentYearId)->where('school_id', $schoolId)->first();

        $activityTypes = CategoryType::with('category')->with('subcategory')->where('allocation_type_id', $allocationType)->get();
        $budgetCategoryId = Arr::pluck($activityTypes, 'category.abbreviation');
        $budgetSubCategoryId = Arr::pluck($activityTypes, 'subcategory.abbreviation');
        $budgetCategoryId = array_merge_recursive($budgetCategoryId, $budgetSubCategoryId);
        $budgetCategoryType = Arr::pluck($activityTypes, 'allocation_type_id');


        $totalCosts = [];
        $allocationSums = [];
        $remaining = [];
        $activityTotals = Budget::groupBy('category_id')
            ->selectRaw('SUM(unit_total_cost) as totalCost, category_id')
            ->where('allocation_type_id', $allocationType)
            ->where('school_id', $schoolId)
            ->whereIn('category_id', $budgetCategoryType)
            ->pluck('totalCost', 'category_id');

        $allocationSums = round($allocation->total_allocation, 2);

        /*foreach ($activityTotals as $key => $totals) {
            $totalCosts[Category::$types[$key]] = round($totals, 2);
            $remaining[Category::$types[$key]] = round($allocationSums[Category::$types[$key]] - $totalCosts[Category::$types[$key]], 2);
        }*/

        return response()->json(['totalsAmount' => ['PD'=>500000,'FE'=>90000], 'usedAmount' => ['PD'=>340000,'FE'=>65000], 'remaining' => ['PD'=>160000,'FE'=>35000]]);
    }

    public function getApprovals()
    {
        $activityApprovalStatus = ApprovalStatus::all();
        $activityApprovalTypes = ApprovalType::all();
        return response()->json(['activityApprovalStatus' => $activityApprovalStatus, 'activityApprovalTypes' => $activityApprovalTypes]);
    }
    
    public function getItemStatus()
    {
        $itemStatus = BudgetItemStatus::all();
        $billingStatus = BillingStatus::all();
        return response()->json(['itemStatus' => $itemStatus, 'billingStatus' => $billingStatus]);
    }
    
    public function getCategoryTrackingList()
    {
        $categoryTracking = CategoryTracking::all();
         return response()->json(['categoryTracking' => $categoryTracking]);
    }

    public function getCategories($allocationId)
    {
        $success = true;
        $errorMessage = '';
        $categoryResponse = [];
        try {
            $category = CategoryType::with('category')->where('allocation_type_id', $allocationId)->get();
            $categoryResponse = ResponseHelper::makeCategoryData($category);
        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['typesCategories' => $categoryResponse,'success' => $success, 'errorMessage' => $errorMessage]);
    }
    
    public function getSuppliersCategoriesStatuses($allocationType)
    {
        $suppliers = Supplier::all();
        $categories = InventoryAllocationType::with('category')->allocation($allocationType)->get();
        //$categories = InventoryCategoryType::where('allocation_type_id', $allocationType)->get();
        $identifications = InventoryIdentificationType::all();
        $statuses = InventoryStatus::all();
        $conditions = InventoryConditionType::all();
        return response()->json(['suppliers' => $suppliers, 'categories' => $categories,
            'identifications' => $identifications, 'status' => $statuses, 'conditions' => $conditions]);
    }

    public function getSubCategories($allocationId)
    {
        $success = true;
        $errorMessage = '';
        $categoryResponse = [];
        try {
            $category = CategoryType::with('subcategory')->where('allocation_type_id', $allocationId)->get();
            $categoryResponse = ResponseHelper::makeCategoryData($category, true);
        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['typesCategories' => $categoryResponse,'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function create($pageType, Request $request)
    {
        $success = true;
        $errorMessage = '';
        $activityResponse = [];
        try {
            $data = $request->all();
            $data['ses_id'] = 101;
            $data['lea_id'] = 2;
            $data['sea_id'] = 30;
            $data['school_year_id'] = 1015;
            $data['item_type_id'] = $pageType;
            $cost = (float)$data['unit_cost'];
            $data['total_cost'] = $cost + $cost * $data['markup_percentage'] / 100;
            $data['markup_fee'] = $cost * $data['markup_percentage'] / 100;
            if ($activity = Budget::create($data)) {
                $detailsData = $data;
                $detailsData['item_id'] = $activity->id;
                $details = BudgetItemDetails::create($detailsData);
             
                $schedule = new Schedule;
                /********/
            }
            $budgetItems = Budget::with('category','subCategory','school','supplier','status','approvalStatus','approvalTypes','details','campus','traking','billing')->
                    where('id', $activity->id)->get();
            //$activity->with('category','subCategory','school','supplier','status','approvalStatus','approvalTypes','details');
            $activityResponse = ResponseHelper::makeBudgetData($budgetItems);
        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }

        return response()->json(['items'=>$activityResponse,'success'=>$success,'errorMessage'=>$errorMessage]);
    }

    public function show($id)
    {
        $activity = Budget::find($id);
        return response()->json($activity);
    }

    public function getRecurranceTypes()
    {
        $recurranceType = ScheduleRecurranceType::all();
        return response()->json(['recurranceType'=>$recurranceType]);
    }

    public function addScheduleToActivity($activityId, Request $request)
    {
        Db::beginTransaction();
        $scheduleResponse = null;
        $recurringResponse = null;
        $recurring = null;
        $success = true;
        $errorMessage = '';
        try {

            $scheduleData = $request->all();
            $scheduleData['ses_id'] = 101;
            $scheduleData['lea_id'] = 2;
            $scheduleData['sea_id'] = 30;
            $scheduleData['start_date'] = date('Y-m-d',strtotime($scheduleData['start_date']));
            $scheduleData['end_date'] = date('Y-m-d',strtotime($scheduleData['end_date']));
            $scheduleData['start_time'] = date('H:i:s',strtotime($scheduleData['start_time']));
            $scheduleData['end_time'] = date('H:i:s',strtotime($scheduleData['end_time']));

            $schedule = Schedule::create($scheduleData);
            $scheduleResponse[] = $schedule;
            $activity = Budget::find($activityId);
            $details = BudgetItemDetails::where('item_id',$activity->id)->first();
            
            if(!$details) {
                $details = new BudgetItemDetails();
                $details->item_id = $activity->id;
            }
            
            if((int)$schedule->is_recurring) {
                $details->has_recurring = 1;
                $recurringData = $scheduleData;
                $recurringData['schedule_id'] = $schedule->id;
                $oldDays = $recurringData['day_of_week'];
                $day_of_week = implode(',',$recurringData['day_of_week']);
                unset($recurringData['day_of_week']);
                $recurringData['day_of_week'] = $day_of_week;

                $recurring = ScheduleRecurrance::create($recurringData);
                $recurringResponse['recurrance_type_id'] = $recurring->recurrance_type_id;
                $recurringResponse['number_of_occurrences'] = (int)$recurring->number_of_occurrences;

                $recurringResponse['day_of_week'] = $oldDays;

            }


            $activityScheduling = ActivitySchedule::create(['budget_id'=>$activity->id, 'schedule_id'=>$schedule->id,'note'=>$scheduleData['note'],'location'=>$scheduleData['location']]);

            /*******************Updateing Activity Start and End dates************************/
            $activitySchedule = $activity->activitySchedule;
            $schedules = [];
            foreach($activitySchedule as $key=>$activSchedule) {
                $schedules[] = $activSchedule->schedule;
            }
            usort($schedules, function($a, $b) {
                return strtotime($a['start_date']) - strtotime($b['start_date']);
            });
            $startDate = $schedules[0]->start_date;
            $endDate = $schedules[count($schedules) - 1]->end_date;
            $activity->start_date = $startDate;
            $activity->end_date = $endDate;
            $details->has_multi_schedule = ($activitySchedule->count() > 1 ) ? 1 : 0; 
            $details->save();
            $activity->save();
            /***************************************************************/


            /*



            $activity = Activity::find($activityId);
            $activitySchedule = $activity->activitySchedule;
            foreach($activitySchedule as $key=>$activSchedule) {
                $schedule = $activSchedule->schedule;
                $schedules[$key] = $schedule;
                if((int)$schedule->is_recurring) {
                    $scheduleRecurrance = ScheduleRecurrance::where('schedule_id',$activSchedule->schedule_id)->get();
                    $scheduleRecType = ScheduleRecurranceType::find($scheduleRecurrance[0]->recurrance_type_id);
                    $schedules[$key]['type'] = $scheduleRecType->recurrance_type;
                    $schedules[$key]['recurrance_type_id'] = $scheduleRecurrance[0]->recurrance_type_id;
                    $schedules[$key]['every'] = $scheduleRecurrance[0]->number_of_occurrences;

                } else {
                    $schedules[$key]['type'] = null;
                    $schedules[$key]['every'] = null;
                }
                $schedules[$key] = $schedule;
                $schedules[$key]['start_date'] = date('Y-m-d',strtotime($schedule->start_date));
                $schedules[$key]['end_date'] = date('Y-m-d',strtotime($schedule->end_date));
                $schedules[$key]['start_time'] = date('H:i:s',strtotime($schedule->start_time));
                $schedules[$key]['end_time'] = date('H:i:s',strtotime($schedule->end_time));
                $schedules[$key]['is_recurring'] = (int)$schedule->is_recurring;
                $schedules[$key]['is_full_day'] = (int)$schedule->is_full_day;
                $schedules[$key]['note'] = $activSchedule->note;
                $schedules[$key]['location'] = $activSchedule->location;


                */



        } catch (Throwable $e) {
            DB::rollBack();
            $success = false;
            $errorMessage = $e->getMessage();
        }
        DB::commit();
        return response()->json(['schedule'=>$schedule,'recurring'=>$recurring,'recurringResponse'=>$recurringResponse,'success'=>$success,'errorMessage'=>$errorMessage]);
    }


    public function editSchedule($scheduleId, $activityId, Request $request)
    {
        Db::beginTransaction();
        $scheduleResponse = null;
        $recurringResponse = null;
        $recurring = null;
        $success = true;
        $errorMessage = '';
        $schedule = [];
        try {
            $scheduleId = (int)$scheduleId;
            $scheduleData = $request->all();
            $schedule = Schedule::find($scheduleId);

            if(!$schedule) {
                $success = false;
                $errorMessage = "Schedule not found";
                return response()->json(['schedule'=>[],'recurring'=>[],'recurringResponse'=>[],'success'=>$success,'errorMessage'=>$errorMessage]);
            }

            $isRecurring = (int)$schedule->is_recurring;

            $scheduleData['ses_id'] = 101;
            $scheduleData['lea_id'] = 2;
            $scheduleData['sea_id'] = 30;
            $scheduleData['start_date'] = date('Y-m-d',strtotime($scheduleData['start_date']));
            $scheduleData['end_date'] = date('Y-m-d',strtotime($scheduleData['end_date']));
            $scheduleData['start_time'] = date('H:i:s',strtotime($scheduleData['start_time']));
            $scheduleData['end_time'] = date('H:i:s',strtotime($scheduleData['end_time']));

            $schedule->update($scheduleData);

            $scheduleResponse[] = $schedule;

            $activity = Budget::find($activityId);
            
            $details = BudgetItemDetails::where('item_id',$activity->id)->first();
            
            if(!$details) {
                $details = new BudgetItemDetails();
                $details->item_id = $activity->id;
            }
            
            if(!$activity) {
                $success = false;
                $errorMessage = "Activity not found";
                return response()->json(['schedule'=>[],'recurring'=>[],'recurringResponse'=>[],'success'=>$success,'errorMessage'=>$errorMessage]);
            }
            
           
            
            $newIsRecured = (int)$scheduleData['is_recurring'];
            if(!$isRecurring && $newIsRecured) {
                $details->has_recurring = 1;
                $recurringData = $scheduleData;
                $recurringData['schedule_id'] = $schedule->id;
                $oldDays = $recurringData['day_of_week'];
                $day_of_week = implode(',',$recurringData['day_of_week']);
                unset($recurringData['day_of_week']);
                $recurringData['day_of_week'] = $day_of_week;

                $recurring = ScheduleRecurrance::create($recurringData);
                $recurringResponse['recurrance_type_id'] = $recurring->recurrance_type_id;
                $recurringResponse['number_of_occurrences'] = (int)$recurring->number_of_occurrences;

                $recurringResponse['day_of_week'] = $oldDays;

            } elseif($isRecurring && !$newIsRecured) {
                $allSchedulesForThisActivity = ActivitySchedule::where('activity_id',$activity->id)->get();
                $isRec = false;
                foreach($allSchedulesForThisActivity as $activitySvchedule) {
                    if($activitySvchedule->recurredSchedules->has_recurring) {
                        $isRec = true;
                        break;
                    }
                }
                $details->has_recurring = (int)$isRec;
            } else {
                $recurringData = $scheduleData;
                $recurringData['schedule_id'] = $schedule->id;
                $oldDays = $recurringData['day_of_week'];
                $day_of_week = implode(',',$recurringData['day_of_week']);
                unset($recurringData['day_of_week']);
                $recurringData['day_of_week'] = $day_of_week;

                $recurring = ScheduleRecurrance::where('schedule_id',$schedule->id)->first();
               if($recurring){
                    $recurring->update($recurringData);
                    $recurringResponse['recurrance_type_id'] = $recurring->recurrance_type_id;
                    $recurringResponse['number_of_occurrences'] = (int)$recurring->number_of_occurrences;

                    $recurringResponse['day_of_week'] = $oldDays;
                }
            }

            $activityScheduling = ActivitySchedule::where('activity_id',$activity->id)->where('schedule_id',$schedule->id)->first();
            $activityScheduling->update($scheduleData);

            /*******************Updateing Activity Start and End dates************************/
            $activitySchedule = $activity->activitySchedule;
            $schedules = [];
            foreach($activitySchedule as $key=>$activSchedule) {
                $schedules[] = $activSchedule->schedule;
            }
            usort($schedules, function($a, $b) {
                return strtotime($a['start_date']) - strtotime($b['start_date']);
            });
            $startDate = $schedules[0]->start_date;
            $endDate = $schedules[count($schedules) - 1]->end_date;
            $activity->start_date = $startDate;
            $activity->end_date = $endDate;
            $details->has_multi_schedule = ($activitySchedule->count() > 1 ) ? 1 : 0;
            $details->save();
             $activity->save();

        } catch (Throwable $e) {
            DB::rollBack();
            $success = false;
            $errorMessage = $e->getMessage();
        }
        DB::commit();
        return response()->json(['schedule'=>$schedule,'recurring'=>$recurring,'recurringResponse'=>$recurringResponse,'success'=>$success,'errorMessage'=>$errorMessage]);
    }

    public function removeScheduleFromActivity($scheduleId)
    {
        $success = true;
        $errorMessage = '';
        try {
            $schedule = Schedule::find($scheduleId);
            ActivitySchedule::where('schedule_id', $schedule->id)->delete();
            $schedule->delete();
        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['success'=>$success,'errorMessage'=>$errorMessage]);
    }

    public function getAttendyTypes()
    {
        $types = ActivityAttendeeType::all();
        return response()->json(['activityAttendyTypes' => $types]);
    }

    public function getAttendies($activityId)
    {
        $attendySuumary = [];
        try {
            $activity = Budget::find($activityId);
            $attendySuumary = $activity->attendeeSummary;

            /*foreach($activityAttendies as $attendee) {
                $attendySuumary[] =  $attendee->summary;
            }*/

            foreach($attendySuumary as $key=>$summary) {
                $summaryType[] =  $summary->type;
                $attendySuumary[$key]->is_all =  (int)$summary->is_all;
            }

        }catch (Throwable $e) {

        }

        return response()->json(['attendySuumary' => $attendySuumary]);
    }

    public function removeAttendy($attendyId)
    {
        $success = true;
        $errorMessage = '';
        try {
            $activityAttendySummary = ActivityAttendeeSummary::find($attendyId);
            if (!$activityAttendySummary) return [];

            $activityAttendy = $activityAttendySummary->attendyRelation;
            foreach($activityAttendy as $attendyRel) {
                $attendyRel->delete();
            }
            $activityAttendySummary->delete();
        } catch (Throwable $e){
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['success'=>$success,'errorMessage'=>$errorMessage]);

    }

    public function addAttendy($activityId, Request $request)
    {
        $success = true;
        $errorMessage = '';
        try {
            $data = $request->all();
            $activity = Budget::find($activityId);
            ActivityAttendeeSummary::where('activity_id',$activity->id)->delete();
            ActivityAttendee::where('activity_id',$activity->id)->delete();
            foreach($data as $values) {
                $activityAttendySummaryData = ['sea_id'=>20,'lea_id'=>34,'ses_id'=>101,
                    'activity_id'=>$activity->id,'count'=>$values['count'],
                    'activity_attendee_type_id'=>$values['type_id'],'is_all'=>(int)$values['is_all']];

                $summary = ActivityAttendeeSummary::create($activityAttendySummaryData);
                if(isset($data['participant']) && count($data['participant'])) {
                    foreach($data['participant'] as $parties) {
                        $activityAttendyData = ['activity_id'=>$activity->id,'activity_attendee_summary_id'=>$summary->id,
                        'activity_school_attendee_list_id'=>$parties,'activity_attendee_type_id'=>$values['type_id']];

                        $activityAttendy = ActivityAttendee::create($activityAttendyData);
                    }
                }
            }

        } catch (Throwable $e){
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['success'=>$success,'errorMessage'=>$errorMessage]);
    }

    public function getAttendyParticipiant($summaryId)
    {
        $success = true;
        $errorMessage = '';
        $attendeeSchool = [];
        try {
            $summary = ActivityAttendeeSummary::find($summaryId);
            $activityAttendy = ActivityAttendee::where('activity_attendee_summary_id', $summary->id)->get();
            foreach($activityAttendy as $attendy) {
                $attendeeSchool[] = $attendy->attendeeSchool;
            }
        } catch (Throwable $e){
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['success'=>$success,'errorMessage'=>$errorMessage,'attendeeSchool'=>$attendeeSchool]);
    }

    public function searchAttendyParticipant($schoolId, Request $request)
    {
        $participantName = $request->get('searchParties') ? $request->get('searchParties') : NULL;

        $limit = $request->get('limit') ? $request->get('limit') : $this->limit;
        $page = $request->get('page') ? $request->get('page') : $this->page;
        $skip = (!$page) ? 0 : ($page - 1) * $limit;
        $participants = [];
        $query = ActivitySchoolAttendeeList::query();
        $query->where('school_id', $schoolId);
        $count = 0;
        if ($participantName) {
             $query->where(function($query) use ($participantName){
                $query->orWhere('first_name', 'like', '%' . $participantName . '%')
                ->orWhere('last_name', 'like', '%' . $participantName . '%')
                ->orWhere('email', 'like', '%' . $participantName . '%');
            });
        }

        $participantCount = $query->count();
        if($participantCount) {
            $participants = $query->skip($skip)->take($limit)->get();
        }

        $pagesCount = ceil($participantCount / $limit);
        return response()->json(['participants' => $participants, 'pagesCount' => $pagesCount]);
    }

    public function addAttendyParticipant($schoolId, $activityId, Request $request)
    {
        $participant = [];
        $success = true;
        $errorMessage = '';
        try {
            $partiesId = (int)$request->get('searchPartiesId');
            $participant = ActivitySchoolAttendeeList::find($partiesId);
            //$data = ['school_id'=>$schoolId,'first_name'=>$first_name,'last_name'=>$parties->last_name,'activity_attendee_type_id'=>$request['type_id']];
            //$participant = ActivitySchoolAttendeeList::create($data);

            $activityAttendyData = ['activity_id'=>$activityId,'activity_attendee_summary_id'=>$request['summary_id'],
                    'activity_school_attendee_list_id'=>$participant->id,'activity_attendee_type_id'=>$request['type_id']];

            $activityAttendy = ActivityAttendee::create($activityAttendyData);


        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }


        return response()->json(['participant' => $participant, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function addParticipantAndAssignToActivity($schoolId, $activityId, Request $request)
    {
        $participant = [];
        $success = true;
        $errorMessage = '';
        try {
            $first_name = $request['first_name'];
            $last_name = $request['last_name'];
            $email = $request['email'];
            $data = ['school_id'=>$schoolId,'first_name'=>$first_name,'last_name'=>$last_name,'activity_attendee_type_id'=>$request['type_id'],'email'=>$email];
            $participant = ActivitySchoolAttendeeList::create($data);

            $activityAttendyData = ['activity_id'=>$activityId,'activity_attendee_summary_id'=>$request['summary_id'],
                    'activity_school_attendee_list_id'=>$participant->id,'activity_attendee_type_id'=>$request['type_id']];

            $activityAttendy = ActivityAttendee::create($activityAttendyData);


        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }


        return response()->json(['participant' => $participant, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function removeAttendyParties($participientId,$activityId)
    {
        $success = true;
        $errorMessage = '';
        try {
            $participient = ActivitySchoolAttendeeList::find($participientId);
            $activityAttendy = ActivityAttendee::where('activity_school_attendee_list_id',$participient->id)->where('activity_id',$activityId)->delete();
        }catch (Throwable $e){
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['success'=>$success,'errorMessage'=>$errorMessage]);
    }

    public function getScheduleForCurrentActivity($activityId)
    {
        $schedulesResponse = [];
        $success = true;
        $errorMessage = '';
        try {
            $schedules = [];
            $activity = Budget::find($activityId);
            $activitySchedule = $activity->activitySchedule;
            foreach($activitySchedule as $key=>$activSchedule) {
                $schedule = $activSchedule->schedule;
                $schedules[$key] = $schedule;
                if((int)$schedule->is_recurring) {
                    $scheduleRecurrance = ScheduleRecurrance::where('schedule_id',$activSchedule->schedule_id)->first();
                    $scheduleRecType = ScheduleRecurranceType::find($scheduleRecurrance->recurrance_type_id);
                    if($scheduleRecType){
                        $schedules[$key]['type'] = $scheduleRecType->recurrance_type ;
                        $schedules[$key]['recurrance_type_id'] = $scheduleRecurrance->recurrance_type_id;
                        $schedules[$key]['every'] = $scheduleRecurrance->number_of_occurrences;
                        $schedules[$key]['repeatOn'] = isset($scheduleRecurrance->getReapeatOnDays()['reapeatOn']) ? $scheduleRecurrance->getReapeatOnDays()['reapeatOn'] : null;
                        $schedules[$key]['reapeatDays'] = isset($scheduleRecurrance->getReapeatOnDays()['reapeatDays']) ? $scheduleRecurrance->getReapeatOnDays()['reapeatDays'] : null;
                    } else {
                         $schedules[$key]['type'] = null;
                        $schedules[$key]['every'] = null;
                        $schedules[$key]['repeatOn'] = null;
                        $schedules[$key]['recurrance_type_id'] = null;
                        $schedules[$key]['reapeatDays'] = null;
                    }
                } else {
                    $schedules[$key]['type'] = null;
                    $schedules[$key]['every'] = null;
                    $schedules[$key]['repeatOn'] = null;
                }
                $schedules[$key] = $schedule;
                $schedules[$key]['start_date'] = date('Y-m-d',strtotime($schedule->start_date));
                $schedules[$key]['end_date'] = date('Y-m-d',strtotime($schedule->end_date));
                $schedules[$key]['start_time'] = date('H:i:s',strtotime($schedule->start_time));
                $schedules[$key]['end_time'] = date('H:i:s',strtotime($schedule->end_time));
                $schedules[$key]['is_recurring'] = (int)$schedule->is_recurring;
                $schedules[$key]['is_full_day'] = (int)$schedule->is_full_day;
                $schedules[$key]['note'] = $activSchedule->note;
                $schedules[$key]['location'] = $activSchedule->location;

            }

            $schedulesResponse = ResponseHelper::makeScheduleData($schedules);

        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
        return response()->json(['schedules'=>$schedulesResponse, 'success' => $success, 'errorMessage' => $errorMessage]);
    }

    public function update(Request $request, $id)
    {
        $activityResponse = [];
        $success = true;
        $errorMessage = '';
        $attributes = [];
        try {
            $activity = Budget::find($id);
            $activityOldInfo = $activity;
            $data = $request->all();
            $cost = (float)$data['unit_cost'];
            $data['total_cost'] = $cost + $cost * $data['markup_percentage'] / 100;
            $data['markup_fee'] = $cost * $data['markup_percentage'] / 100;
            
            

            /*
            upcharge_percentage = 1,12
                fee = 1,12
                5000 , 5600
              600
    /*
                $data['purchase_date'] = isset($data['purchase_date']) ? date('Y-m-d H:i:s',strtotime($data['purchase_date'])) : date('Y-m-d H:i:s',strtotime($license->purchase_date));
                $data['expiration_date'] = isset($data['expiration_date']) ? date('Y-m-d H:i:s',strtotime($data['expiration_date'])) : date('Y-m-d H:i:s',strtotime($license->expiration_date));
                $data['renewal_date'] = isset($data['renewal_date']) ? date('Y-m-d H:i:s',strtotime($data['renewal_date'])) : date('Y-m-d H:i:s',strtotime($license->renewal_date));;

                */

            if($update = $activity->update($data)) {
            
                $details = BudgetItemDetails::where('item_id',$activity->id);
                $attributes = $details->attributesToArray();
                echo "<pre>";print_r($attributes);die;
                $detailsData['has_recurring'] = $data;
                $detailsData['has_multi_schedule'] = $data;
                $detailsData['has_recurring'] = $data;
                $detailsData['has_recurring'] = $data;
                
                $details->update($detailsData); 
            
                if($activityOldInfo->activity_approval_status_id != $data['activity_approval_status_id'])
                {
    
                }
            }
            
            
            $budgetItems = Budget::with('category','subCategory','school','supplier','status','approvalStatus','approvalTypes','details','campus','traking','billing')->
                    where('id', $activity->id)->get();
            $activityResponse = ResponseHelper::makeBudgetData($budgetItems);
        } catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }


        return response()->json(['items'=>$activityResponse,'success'=>$success,'errorMessage'=>$errorMessage,'attributes'=>$attributes]);
    }

    public function destroy($id)
    {
        $success = true;
        $errorMessage = '';
        try {
            $activity = Budget::find($id);
            if (!$activity) return [];

            $schedules = [];
            $activitySchedule = $activity->activitySchedule;


            if ($activitySchedule->count()) {
                 foreach ($activitySchedule as $schedule) {
                        $schedules[] = $schedule->schedule;
                        $schedule->delete();
                    }
                if ($schedules) {
                    foreach ($schedules as $schedule) {
                        $schedule->delete();
                    }
                }
                foreach ($activitySchedule as $schedule) {
                    $schedule->delete();
                }
            }


            $activity->delete();
        }catch (Throwable $e) {
            $success = false;
            $errorMessage = $e->getMessage();
        }
         return response()->json(['success'=>$success,'errorMessage'=>$errorMessage]);
    }

}
