<?php

	function datetime_convert($time, $to_timezone=null, $from_timezone=null) {


		if ($from_timezone === null) {
			$from_timezone = date_default_timezone_get();
		}

		if ($to_timezone === null) {
			$to_timezone = !empty(user::$data['timezone']) ? user::$data['timezone'] : date_default_timezone_get();
		}

		$timestamp = new \DateTime($time, new \DateTimeZone($from_timezone));

		$timestamp->setTimezone(new \DateTimeZone($to_timezone));

		return $timestamp->getTimestamp();
	}

/*
	function datetime_ago($time) {

		$current_time = new DateTime();
		$timestamp = new DateTime(@$time);
		$diff = $current_time->diff($timestamp);

		foreach ([
			'y' => 'year',
			'm' => 'month',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second'
		] as $symbol => $description) {
			if ($diff->$symbol > 0) {
				$unit = $diff->$symbol;
				return $unit . ' ' . ($unit > 1 ? $description . 's' : $description) . ' ago';
			}
		}

		return language::translate('text_just_now', 'Just now');
	}
*/

	function datetime_ago($timestamp, $present=30, $present_output=null) {

		if (!date_is_timestamp($timestamp)) {
			if (!$timestamp = strtotime($timestamp)) return false;
		}

		$time_elapsed = time() - $timestamp;

		$seconds = $time_elapsed;
		$minutes = round($time_elapsed / 60);
		$hours   = round($time_elapsed / 3600);
		$days    = round($time_elapsed / 86400);
		$weeks   = round($time_elapsed / 604800);
		$months  = round($time_elapsed / 2600640);
		$years   = round($time_elapsed / 31207680);

	// Seconds
		if ($seconds <= 60) {
			if ($present_output === null) {
				$present_output = '<span style="color: #0a0;">'. language::translate('text_just_now', 'Just now') .'</span>';
			}
			return $present_output;
		}

		// Minutes
		else if ($minutes <= 60) {
			return strtr(language::translate('text_n_time_ago', '%n %unit ago'), ['%n' => $minutes, '%unit' => language::translate('time_unit_min', 'min')]);
		}

		// Hours
		else if ($hours <= 24) {
			return strtr(language::translate('text_n_time_ago', '%n %unit ago'), ['%n' => $hours, '%unit' => language::translate('time_unit_h', 'h')]);
		}

		// Days
		else if ($days <= 7) {
			if ($days == 1) {
				return language::translate('title_yesterday', 'Yesterday');
				//return strtr(language::translate('title_yesterday', 'Yesterday') . ' %time', ['%time' => language::strftime(language::$selected['format_time'], $timestamp)]);
			} else {
				return strtr(language::translate('text_n_time_ago', '%n %unit ago'), ['%n' => $days, '%unit' => language::translate('time_unit_days', 'days')]);
			}
		}

		// Weeks
		else if ($weeks <= 4.3) {
			if ($weeks==1) {
				return 'A week ago';
			} else {
				return strtr(language::translate('text_n_time_ago', '%n %unit ago'), ['%n' => $weeks, '%unit' => language::translate('time_unit_weeks', 'weeks')]);
			}
		}

		// Months
		else if ($months <= 12) {
			if ($months == 1) {
				return 'A month ago';
			} else {
				return strtr(language::translate('text_n_time_ago', '%n %unit ago'), ['%n' => $months, '%unit' => language::translate('time_unit_months', 'months')]);
			}
		}

		// Years
		else if ($years == 1) {
			return 'A year ago';
		}

		// Ages ago
		else if ($years > 100) {
			return language::translate('text_when_dinosaurs_roamed_the_earth', 'When dinosaurs roamed the Earth');
		}

		return strtr(language::translate('text_n_time_ago', '%n %unit ago'), ['%n' => $years, '%unit' => language::translate('time_unit_years', 'years')]);
	}

	function datetime_age($date) {

		$date = new DateTime($date);
		$currentDate = new DateTime();
		$age = $currentDate->diff($date);

		return $age->y;
	}

	// Returns the last point in time by step interval
	function datetime_last_by_interval($interval, $timestamp=null) {

		if ($timestamp === null) {
			$timestamp = time();

		} else if (!is_numeric($timestamp)) {
			$timestamp = strtotime($timestamp);
		}

		$y = date('Y', $timestamp);
		$m = date('m', $timestamp);
		$d = date('m', $timestamp);

		switch (true) {

			case (strcasecmp($interval, '5 min')):       return mktime(date('H'), floor(date('i', $timestamp) /5)  *5, 0, $m, $d, $y);
			case (strcasecmp($interval, '10 min')):      return mktime(date('H'), floor(date('i', $timestamp) /10) *10, 0, $m, $d, $y);
			case (strcasecmp($interval, '15 min')):      return mktime(date('H'), floor(date('i', $timestamp) /15) *15, 0, $m, $d, $y);
			case (strcasecmp($interval, '30 min')):      return mktime(date('H'), floor(date('i', $timestamp) /30) *30, 0, $m, $d, $y);
			case (strcasecmp($interval, 'Hourly')):      return mktime(date('H'), 0, 0, $m, $d, $y);
			case (strcasecmp($interval, '2 hours')):     return mktime(floor(date('H', $timestamp) /2)  *2,  0, 0, $m, $d, $y);
			case (strcasecmp($interval, '3 hours')):     return mktime(floor(date('H', $timestamp) /3)  *3,  0, 0, $m, $d, $y);
			case (strcasecmp($interval, '6 hours')):     return mktime(floor(date('H', $timestamp) /6)  *6,  0, 0, $m, $d, $y);
			case (strcasecmp($interval, '12 hours')):    return mktime(floor(date('H', $timestamp) /12) *12, 0, 0, $m, $d, $y);
			case (strcasecmp($interval, 'Daily')):       return mktime(0, 0, 0, $m, $d, $y);
			case (strcasecmp($interval, 'Weekly')):      return strtotime('This week 00:00:00', $timestamp);
			case (strcasecmp($interval, 'Monthly')):     return mktime(0, 0, 0, null, 1, $y);
			case (strcasecmp($interval, 'Quarterly')):   return mktime(0, 0, 0, ((ceil(date('n', $timestamp) /3) -1) *3) +1, $d, $y);
			case (strcasecmp($interval, 'Half-Yearly')): return mktime(0, 0, 0, ((ceil(date('n', $timestamp) /6) -1) *6) +1, $d, $y);
			case (strcasecmp($interval, 'Yearly')):      return mktime(0, 0, 0, 1, 1, $y);

			default: trigger_error('Unknown step interval ('. $interval .')', E_USER_WARNING); return false;
		}
	}
