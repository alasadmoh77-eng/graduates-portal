<?php
namespace App\Enums;

enum RequestStatus: string {
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case READY = 'ready';
    case DELIVERED = 'delivered';
}
