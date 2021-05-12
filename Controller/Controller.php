<?php

namespace Cis\EducationalVisitBundle\Controller;

use Petroc\Component\View\ViewTrait;

class Controller
{
    const ACCESS_RULE = 'cis_educational_visit';
    const ACCESS_RULE_EDIT = 'cis_educational_visit.edit';
    const ACCESS_RULE_ADMIN = 'cis_educational_visit.admin';
    const ACCESS_RULE_FINANCE = 'cis_educational_visit.finance';
    const ACCESS_RULE_STUDENT_PARTICIPANT_VISIT_CONSENT = 'cis_educational_visit.student_participant_visit_consent';
    const ACCESS_RULE_STUDENT = 'cis_educational_visit.student';
    const ACCESS_RULE_ITINERARY_DOWNLOAD = 'cis_educational_visit.itinerary.download';
    const ACCESS_RULE_APPROVAL_REQUEST = 'cis_educational_visit.approval.request';
    const ACCESS_RULE_APPROVAL_CAN_APPROVE = 'cis_educational_visit.approval.can_approve';


    const ROUTE_INDEX = 'cis_educational_visit';
    const ROUTE_DETAIL = 'cis_educational_visit.detail';
    const ROUTE_EXPENSES_AND_INCOME = 'cis_educational_visit.expenses_and_income';
    const ROUTE_EXPENSES_AND_INCOME_EDIT = 'cis_educational_visit.expenses_and_income.edit';
    const ROUTE_PARTICIPANTS = 'cis_educational_visit.participants';
    const ROUTE_PAYMENTS = 'cis_educational_visit.payments';
    const ROUTE_STUDENT = 'cis_educational_visit.student';
    const ROUTE_ITINERARY = 'cis_educational_visit.itinerary';
    const ROUTE_REFRESH_ALERTS = 'cis_user.refresh_alerts';

    use ViewTrait;
    
}
