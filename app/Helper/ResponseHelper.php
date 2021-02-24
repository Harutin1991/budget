<?php


namespace App\Helper;

class ResponseHelper
{
    const OK = 200;
    const UNAUTHORIZED = 401;
    const UNPROCESSABLE_ENTITY_EXPLAINED = 422;

    public static function success($data, $pagination = null, $msg = null)
    {
        if($pagination == null) {
            $response = array(
                "message" => $msg ?? "",
                "data" => $data,
                "status" => true
            );
        } else {
            $data = $data->toArray();
            $response = array(
                "message" => $msg ?? "",
                "data" => array(
                    "list" => $data["data"],
                    "meta" => array(
                        "page" => $data['current_page'],
                        "limit" => intval($data['per_page']),
                        "total" => $data['total'],
                        "last_page" => $data["last_page"]
                    ),
                ),
                "status" => true
            );
        }
        return response()->json($response, 200);
    }

    public static function makeBudgetData($budgetItems)
    {
        $activityResponse = [];
        if(!isset($budgetItems->id)) {
            foreach ($budgetItems as $key => $activity) {
                $activityResponse[$key] = $activity;
                $activityResponse[$key]['total_cost'] = round($activity->unit_total_cost,2);
                $activityResponse[$key]['cost'] = round($activity->unit_cost,2);
                

                $activityResponse[$key]['is_online'] = ($activity->details) ? (int)$activity->details->is_online : 0;
                $activityResponse[$key]['has_multi_schedule'] = ($activity->details) ? (int)$activity->details->has_multi_schedule: 0;
                $activityResponse[$key]['has_recurring'] = ($activity->details) ? (int)$activity->details->has_recurring: 0;
                $activityResponse[$key]['start_date'] = ($activity->start_date) ? date('m-d-Y',strtotime($activity->start_date)) : null;
                $activityResponse[$key]['end_date'] = ($activity->end_date) ? date('m-d-Y',strtotime($activity->end_date)) : null;
                $activityResponse[$key]['completed_date'] = ($activity->completed_date) ? date('m-d-Y',strtotime($activity->completed_date)) : null;
                $activityResponse[$key]['attendySummary'] = $activity->getAttendySummary();
            }
        } else {
            $activity = $budgetItems;
            $activityResponse[0] = $activity;
            $activityResponse[0]['is_online'] = ($activity->details) ? (int)$activity->details->is_online: 0;
            $activityResponse[0]['start_date'] = ($activity->start_date) ? date('m-d-Y',strtotime($activity->start_date)) : null;
            $activityResponse[0]['end_date'] = ($activity->end_date) ? date('m-d-Y',strtotime($activity->end_date)) : null;
            $activityResponse[0]['attendySummary'] = $activity->getAttendySummary();
            $activityResponse[0]['has_multi_schedule'] = ($activity->details) ? (int)$activity->details->has_multi_schedule: 0;
            $activityResponse[0]['has_recurring'] = ($activity->details) ? (int)$activity->details->has_recurring: 0;
        }
        return $activityResponse;
    }

    public static function makeCategoryData($categories = [], $isSub = false)
    {
        $categoryResponse = [];
        foreach ($categories as $category)
        {
            if(!$isSub && $category->category) {
                $categoryResponse[] = $category->category;
            }
            if($isSub && $category->subcategory){
                $categoryResponse[] = $category->subcategory;
            }
        }
        return $categoryResponse;
    }

    public function makeAttendyData($activityAttendies,&$attendySummary)
    {
        $attendySuumaryResult = [];
        $attendySummary = [];
       foreach($activityAttendies as $attendee) {
                $attendySummary[] =  $attendee->summary;
            }

            foreach($attendySummary as $key=>$summary) {
                $summaryType[] =  $summary->type;
                $attendySummary[$key]->is_all = (int)$summary->is_all;
            }

        return $attendySummary;
    }

    public static function makeScheduleData($scheduleData)
    {
        return $scheduleData;
    }

    public static function fail($msg, $code)
    {
        $response = array(
            "message" => $msg ?? "",
            "data" => array(),
            "status" => false
        );
        return response()->json($response, $code);
    }
}
