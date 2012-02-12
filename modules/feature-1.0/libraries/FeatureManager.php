<?php defined('SYSPATH') or die('No direct access allowed.');

class FeatureManager
{
    public static function provideNumberTargets()
    {
        $target_objects = array();

        $features = Doctrine::getTable('Feature')->findAll(Doctrine::HYDRATE_ARRAY);

        foreach ($features as $feature)
        {
            $target_objects[] = array(
                'object_name' => $feature['ftr_display_name'],
                'object_description' => 'Feature',
                'object_number_type' => 'FeatureNumber',
                'object_id' =>  $feature['ftr_id'],
            );
        }

        Event::$data['FeatureNumber'] = array(
            'short_name' => 'feature',
            'display_name' => 'Feature',
            'target_objects' => $target_objects,
            'quick_add' => '/feature/create'
        );
    }

    public static function provideNumberOptions()
    {
        Event::$data['FeatureNumber'] = 'feature/featureOptions';
    }

	public function installDefaultFeatures()
    {
		try {
			Feature::reregister(
				'ivrreturn',
				'feature',
				'Return to last Auto Attendent',
				'Return to the last Auto Attendant',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'redial',
				'feature',
				'Redial',
				'Redial the last number dialed',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'callreturn',
				'feature',
				'Call Return',
				'Dial the last number that called',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'checkvmaskuseraskpass',
				'feature',
				'Voicemail',
				'Dial Voicemail and ask for extension and password',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'checkvmautouseraskpass',
				'feature',
				'Voicemail - Sip user - Ask Password',
				'Dial Voicemail using the calling sip user as the voicemail user. The Sip user and voicemail user must be in sync for this to work.',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'checkvmautouserautopass',
				'feature',
				'Voicemail - Sip User - Sip Password',
				'Dial Voicemail using the calling Sip user as the voicemail user and Sip password as the password.  The Sip user and password must be in sync between the device and voicemail for this to work.',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'park',
				'feature',
				'Call Park',
				'Tranfer a call to a park extension',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'unpark',
				'feature',
				'Call Unpark/Pickup',
				'Unpark/Pickup a call from a parked extension',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'echo',
				'feature',
				'Echo Test',
				'Echo back every sound (helpful in determining if the phone is working and/or is full duplex)',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'delayecho',
				'feature',
				'Delay Echo',
				'Echo back every sound with a 1 second delay (helpful in determining if the phone is working while preventing feedback on phones that are not full duplex)',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'tonetest',
				'feature',
				'Tone Test',
				'Play a tone',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'holdmusic',
				'feature',
				'Hold Music' ,
				'Listen to the default system hold music (to verify that it is working)',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'forwardon',
				'feature',
				'Forward On' ,
				'Forward this extension to another',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}

		try {
			Feature::reregister(
				'forwardoff',
				'feature',
				'Forward Off' ,
				'Un-Forward this extension to another',
				User::TYPE_SYSTEM_ADMIN
			);
		} catch (FeatureException $e) {
			if ($e->getCode() != 0 || $e->getCode() == -2)
				throw $e;
		}
	}
}