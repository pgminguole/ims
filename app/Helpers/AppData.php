<?php

use App\Models\Category;
use App\Models\Location;
use App\Models\Registry;
use Carbon\Carbon;
use Illuminate\Support\Str;

function assetStatus(){

    return [
//        'Active',
        'Occupied',
        'Unoccupied',
        'Under Maintenance',
        'Decommissioned',
        'Draft',
    ];
}

function assetState(){

    return [
        'pending',
        'stage_1_approved',
        'stage_2_approved',
        'approved'
    ];
}

function assetCondition()
{
    return [
        "New",
        "Fairly New",
        "Dilapidated"
    ];
}

function assetOwnership()
{
    return [
        "Judicial Service",
        "MMDA",
        "SSNIT",
        "TDC",
        "Others"
    ];
}

function status(){

    return [
        'Published',
        'Draft',
    ];
}

function editStatus(){

    return [
        'Published',
        'Draft',
        'Move to trash',
    ];
}


function appStatus(){

    return [
        'Pending',
        'Draft',
    ];
}

function editAppStatus(){

    return [
        'Pending',
        'Draft',
        'Released',
        'Move to trash',
    ];
}


function user_status(){

    return [
        'Active' => "Active",
        'Suspended' => "Suspended",
        'Banned' => "Blocked",
    ];
}


function access_level(){

    return [
        'Staff' => "Staff",
        'Director' => "Director",
        'Management' => "Management",
        'Developer' => "Developer",
        'General Admin' => "General Admin",
        'System Admin' => "System Admin",
    ];
}

function role_status(){

    return [
        'Active' => "Active",
        'Inactive' => "Inactive",
    ];
}

function regions(){
    return[
        'AF',
        'AH',
        'BO',
        'BE',
        'CP',
        'EP',
        'AA',
        'NP',
        'NE',
        'OT',
        'SV',
        'UE',
        'UW',
        'TV',
        'WP',
        'WN',
    ];
}

function fetch_locations($id){

    return Location::query()->where('courttype_id', $id)->get();
}

function fetch_registries($id){

    return Registry::query()->where('location_id', $id)->get();
}


/**
 * @param $date
 * @return string
 */
function getCustomLocalTime($date){

    $newDateTime = date('g:i A', strtotime($date));

    $new_date =  $date->format('d M, Y') .' at '. $newDateTime;

    return $new_date;
}


function getCustomLocalDate($date){

    $new_date =  Carbon::parse($date)->format('d M, Y');

    return $new_date;
}


// function turn_around_time($bail){


// 	$start_date = $bail->date_granted;
// 	$end_date = $bail->released_date;

// 	$startDateTime = Carbon::parse($start_date);
// 	$endDateTime = Carbon::parse($end_date);

// 	$turnaroundTime = $endDateTime->diffForHumans($startDateTime);


// 	return $turnaroundTime;

// }




function legalYear(){

    // Get the current date
    $currentDate = Carbon::now();
    $currentYear = Carbon::now()->year;

    // Check if the current date is on or after October 1st of the current year
    if ($currentDate->month >= 10) {
        // If it is, set the legal year start to October 1st of the current year
        $legalYearStart = Carbon::create($currentDate->year, 10, 1);
        $legalYearEnd = $legalYearStart->copy()->addYear()->subDay();
    } else {
        // If it's before October 1st, set the legal year start to October 1st of the previous year
        $legalYearStart = Carbon::create($currentDate->year - 1, 10, 1);
        $legalYearEnd = Carbon::create($currentDate->year, 9, 30);
    }


    return [
        'currentDate' => $currentDate,
        'currentYear' => $currentYear,
        'legalYearStart' => $legalYearStart,
        'legalYearEnd' => $legalYearEnd,
    ];
}


function subcategories($category)
{
    $subCategory = [];

    if ($category){

        $category = Category::query()->with('children')->where('name', $category)->first();

        $subCategory = $category->children;

    }

    return $subCategory;
}


function furnitureMaterials()
{
    return  [
        'Wood',
        'Metal',
        'Plastic',
    ];
}

function fuelType()
{
    return  [
        'Diesel',
        'Petrol',
        'Electric',
        'Plug-in Hybrid',
        'Biodiesel',
        'LPG',
        'CNG',
        'Ethanol',
        'Hydrogen',
    ];
}


function generateAssetId($category = null)
{
    //generate asset id
    return 'JSG/'.getInitials($category). '/' . date('y') . '/' . Str::random(6);
}

function getInitials($name)
{
    $words = explode(' ', $name);
    $initials = '';

    foreach ($words as $word) {
        $initials .= substr($word, 0, 1);
    }

    return strtoupper($initials); // Convert to uppercase if desired
}
