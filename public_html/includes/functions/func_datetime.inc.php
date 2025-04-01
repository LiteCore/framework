<?php

	function datetime_age($date, $timezone=null) {
		return date_diff(date_create($date, $timezone), date_create())->y;
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

		if (!is_numeric($timestamp)) {
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

		return strtr(language::translate('text_n_time_ago', '%n %unit ago'), [
			'%n' => $years,
			'%unit' => language::translate('time_unit_years', 'years')
		]);
	}

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

	// Returns a stfrftime-style formatted date-time string
	function datetime_format($format, $timestamp=null) {

		if ($timestamp === null) {
			$timestamp = new \DateTime();

		} elseif (is_numeric($timestamp)) {
			$timestamp = new \DateTime('@' . $timestamp, new DateTimeZone('UTC'));
			$timestamp->setTimezone(new DateTimeZone(date_default_timezone_get()));

		} elseif (is_string($timestamp)) {
			$timestamp = new \DateTime($timestamp);
		}

		if (!extension_loaded('intl')) {
			trigger_error('You need the PHP Intl extension enabled to format dates', E_USER_WARNING);
			return date('Y-m-d H:i:s', $timestamp);
		}

		if (!($timestamp instanceof \DateTimeInterface)) {
			trigger_error('$timestamp argument is neither a valid UNIX timestamp, a valid date-time string or a DateTime object.', E_USER_WARNING);
			return 'n/a';
		}

		// Format aliases
		$format = strtr($format, [
			'datetime' => language::$selected['format_datetime'],
			'date' => language::$selected['format_date'],
			'time' => language::$selected['format_time'],
		]);

		$intl_formats = [
			'%a' => 'EEE',	// An abbreviated textual representation of the day	Sun through Sat
			'%A' => 'EEEE',	// A full textual representation of the day	Sunday through Saturday
			'%b' => 'MMM',	// Abbreviated month name, based on the locale	Jan through Dec
			'%B' => 'MMMM',	// Full month name, based on the locale	January through December
			'%h' => 'MMM',	// Abbreviated month name, based on the locale (an alias of %b)	Jan through Dec
			'%p' => 'aa',	// UPPER-CASE 'AM' or 'PM' based on the given time	Example: AM for 00:31, PM for 22:23
			'%P' => 'aa',	// lower-case 'am' or 'pm' based on the given time	Example: am for 00:31, pm for 22:23
		];

		$intl_formatter = function (\DateTimeInterface $timestamp, string $format) use ($intl_formats) {
			$tz = $timestamp->getTimezone();
			$date_type = IntlDateFormatter::FULL;
			$time_type = IntlDateFormatter::FULL;
			$pattern = '';

			// %c = Preferred date and time stamp based on locale
			// Example: Tue Feb 5 00:45:10 2009 for February 5, 2009 at 12:45:10 AM
			switch ($format) {

				case '%c':
					$date_type = IntlDateFormatter::LONG;
					$time_type = IntlDateFormatter::SHORT;
					break;

				// %x = Preferred date representation based on locale, without the time
				// Example: 02/05/09 for February 5, 2009
				case '%x':
					$date_type = IntlDateFormatter::SHORT;
					$time_type = IntlDateFormatter::NONE;
					break;

				// Localized time format
				case '%X':
					$date_type = IntlDateFormatter::NONE;
					$time_type = IntlDateFormatter::MEDIUM;
					break;

				default:
					$pattern = $intl_formats[$format];
					break;
			}

			return (new IntlDateFormatter(language::$selected['code'], $date_type, $time_type, $tz, null, $pattern))->format($timestamp);
		};

		$mappings = [
			// Day
			'%a' => $intl_formatter,
			'%A' => $intl_formatter,
			'%d' => 'd',
			'%e' => 'j',

			'%j' => function ($timestamp) { // Day number in year, 001 to 366
				return sprintf('%03d', $timestamp->format('z')+1);
			},
			'%u' => 'N',
			'%w' => 'w',

			// Week
			'%U' => function ($timestamp) { // Number of weeks between date and first Sunday of year
				$day = new \DateTime(sprintf('%d-01 Sunday', $timestamp->format('Y')));
				return intval(($timestamp->format('z') - $day->format('z')) / 7);
			},

			'%W' => function ($timestamp) { // Number of weeks between date and first Monday of year
				$day = new \DateTime(sprintf('%d-01 Monday', $timestamp->format('Y')));
				return intval(($timestamp->format('z') - $day->format('z')) / 7);
			},
			'%V' => 'W',

			// Month
			'%b' => $intl_formatter,
			'%B' => $intl_formatter,
			'%h' => $intl_formatter,
			'%m' => 'm',

			// Year
			'%C' => function ($timestamp) { // Century (-1): 19 for 20th century
				return (int) $timestamp->format('Y') / 100;
			},
			'%g' => function ($timestamp) {
				return substr($timestamp->format('o'), -2);
			},
			'%G' => 'o',
			'%y' => 'y',
			'%Y' => 'Y',

			// Time
			'%H' => 'H',
			'%k' => 'G',
			'%I' => 'h',
			'%l' => 'g',
			'%M' => 'i',
			'%p' => $intl_formatter, // AM PM (this is reversed on purpose!)
			'%P' => $intl_formatter, // am pm
			'%r' => 'G:i:s A', // %I:%M:%S %p
			'%R' => 'H:i', // %H:%M
			'%S' => 's',
			'%X' => $intl_formatter, // Preferred time representation based on locale, without the date

			// Timezone
			'%z' => 'O',
			'%Z' => 'T',

			// Time and Date Stamps
			'%c' => $intl_formatter,
			'%D' => 'm/d/Y',
			'%F' => 'Y-m-d',
			'%s' => 'U',
			'%x' => $intl_formatter,
		];

		$out = preg_replace_callback('/(?<!%)(%[a-zA-Z])/', function ($match) use ($mappings, $timestamp) {
			if ($match[1] == '%n') {
				return "\n";
			} else if ($match[1] == '%t') {
				return "\t";
			}

			if (!isset($mappings[$match[1]])) {
				throw new \InvalidArgumentException(sprintf('Format "%s" is unknown in time format', $match[1]));
			}

			$replace = $mappings[$match[1]];

			if (is_string($replace)) {
				return $timestamp->format($replace);
			} else {
				return $replace($timestamp, $match[1]);
			}
		}, $format);

		$out = str_replace('%%', '%', $out);
		return $out;
	}

	function datetime_when($timestamp=null) {

		if ($timestamp === null) {
			$timestamp = new \DateTime();

		} elseif (is_numeric($timestamp)) {
			$timestamp = new \DateTime('@' . $timestamp, new DateTimeZone('UTC'));
			$timestamp->setTimezone(new DateTimeZone(date_default_timezone_get()));

		} elseif (is_string($timestamp)) {
			$timestamp = new \DateTime($timestamp);
		}

		// If ahead of now
		if ($timestamp > new \DateTime()) {

			// If later today
			if ($timestamp > (new \DateTime())->setTime(0, 0)) {
				return datetime_format('time', $timestamp);
			}

			// If tomorrow
			if ($timestamp > (new \DateTime())->modify('+1 day')->setTime(0, 0)) {
				return language::translate('text_tomorrow', 'Tomorrow') . ' ' . datetime_format('time', $timestamp);
			}

			return datetime_format('datetime', $timestamp);
		}

		if ($timestamp > (new \DateTime())->modify('-1 minute')) {
			return language::translate('text_just_now', 'Just now');
		}

		if ($timestamp > (new \DateTime())->modify('-1 hour')) {
			return strtr(language::translate('text_n_minutes_ago', '%n minutes_ago'), ['%n' => (new \DateTime())->diff($timestamp)->i]);
		}

		if ($timestamp > (new \DateTime())->setTime(0, 0)) {
			return language::translate('text_today', 'Today') . ' ' . datetime_format('time', $timestamp);
		}

		if ($timestamp > (new \DateTime())->modify('-1 day')->setTime(0, 0)) {
			return language::translate('text_yesterday', 'Yesterday') . ' ' . datetime_format('time', $timestamp);
		}

		return datetime_format('datetime', $timestamp);
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

			case (!$interval): return false;

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
			case (strcasecmp($interval, '3 months')):
			case (strcasecmp($interval, 'Quarterly')):   return mktime(0, 0, 0, ((ceil(date('n', $timestamp) /3) -1) *3) +1, $d, $y);
			case (strcasecmp($interval, '6 months')):
			case (strcasecmp($interval, 'Half-Yearly')): return mktime(0, 0, 0, ((ceil(date('n', $timestamp) /6) -1) *6) +1, $d, $y);
			case (strcasecmp($interval, '12 months')):
			case (strcasecmp($interval, 'Yearly')):      return mktime(0, 0, 0, 1, 1, $y);

			default: trigger_error('Unknown step interval ('. $interval .')', E_USER_WARNING); return false;
		}
	}
