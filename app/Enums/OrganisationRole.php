<?php

namespace App\Enums;

enum OrganisationRole: string
{
    case SuperAdmin = 'SUPER_ADMIN';
    case OrganisationAdmin = 'ORGANISATION_ADMIN';
    case Researcher = 'RESEARCHER';
    case ExpertReviewer = 'EXPERT_REVIEWER';
    case Observer = 'OBSERVER';
}
