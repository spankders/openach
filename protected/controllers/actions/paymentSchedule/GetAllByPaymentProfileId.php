<?php
/*********************************************************************************
 * OpenACH is an ACH payment processing platform
 * Copyright (C) 2011 Steven Brendtro, ALL RIGHTS RESERVED
 * 
 * Refer to /legal/license.txt for license information, or view the full license
 * online at http://openach.com/community/license.txt
 ********************************************************************************/

class GetAllByPaymentProfileId extends OAApiAction
{

        public function run($payment_profile_id)
        {
                $this->assertAuth();

                $criteria = new CDbCriteria();
                $criteria->together = true; //with( array( 'payment_profile' ) );
                $criteria->addCondition( 'payment_profile_id = :payment_profile_id' );
                $criteria->addCondition( 'payment_profile_originator_info_id = :originator_info_id' );
                //$criteria->addCondition( 'payment_schedule_end_date >= NOW()' );
                $criteria->join = 'LEFT JOIN external_account ON external_account_id = payment_schedule_external_account_id ';
                $criteria->join .= 'LEFT JOIN payment_profile ON payment_profile_id = external_account_payment_profile_id ';
                $criteria->params = array(
                                ':payment_profile_id' => $payment_profile_id,
                                ':originator_info_id' => $this->userApi->user_api_originator_info_id,
                        );

                $models = PaymentSchedule::model()->findAll( $criteria );
                if ( $models )
                {
                        $exported = array();
                        foreach ( $models as $model )
                        {
                                $exported[] = $model->apiExport();
                        }
                        echo json_encode( $this->formatSuccess( $exported ) );
                }
                else
                {
			echo json_encode( $this->formatSuccess( array() ) );
                }
                Yii::app()->end();
        }

}
