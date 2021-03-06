<?php
/**
 * Copyright 2017 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once __DIR__ . '/../../BaseExample.php';

/**
 * This example illustrates how to create a Account-level Filter Set.
 */
class CreateAccountLevelFilterSet extends BaseExample
{

  /**
   * Date format used in this example when converting between DateTime instances
   * and Strings.
   */
  private static $dateFormat = 'Ymd';

  /**
   * Returns a valid AbsoluteDateRange with the given inputs. If the given
   * inputs are both set to null, this will create an AbsoluteDateRange for
   * the past week.
   *
   * @param string $startDateString The start date represented in YYYYMMDD
   *     format
   * @param string $endDateString The end date represented in YYYYMMDD format
   * @return Google_Service_AdExchangeBuyerII_AbsoluteDateRange An
   *     AbsoluteDateRange with start and end Dates based on the given
   *     startDateString and endDateString
   */
  private function getAbsoluteDateRange($startDateString, $endDateString) {
    $startDateTime = null;
    $endDateTime = null;

    if (is_null($startDateString) && is_null($endDateString)) {
      $startDateTime = new DateTime('-7 days');
      $endDateTime = new DateTime('today');
    } else if (is_null($startDateString) || is_null($endDateString)) {
      throw new InvalidArgumentException('Both startDate and endDate must be '
          . 'set.');
    } else {
      $startDateTime = DateTime::createFromFormat($this::$dateFormat,
          $startDateString);
      $endDateTime = DateTime::createFromFormat($this::$dateFormat,
          $endDateString);
    }

    $difference = $startDateTime->diff($endDateTime);

    if ($difference->days < 0) {
      throw new InvalidArgumentException('Start date must be before the end '
          . 'date.');
    }

    $startDate = new Google_Service_AdExchangeBuyerII_Date();
    $startDate->year = $startDateTime->format('Y');
    $startDate->month = $startDateTime->format('m');
    $startDate->day = $startDateTime->format('d');
    $endDate = new Google_Service_AdExchangeBuyerII_Date();
    $endDate->year = $endDateTime->format('Y');
    $endDate->month = $endDateTime->format('m');
    $endDate->day = $endDateTime->format('d');

    $absoluteDateRange =
        new Google_Service_AdExchangeBuyerII_AbsoluteDateRange();
    $absoluteDateRange->setStartDate($startDate);
    $absoluteDateRange->setEndDate($endDate);

    return $absoluteDateRange;
  }

  /**
   * @see BaseExample::getInputParameters()
   */
  protected function getInputParameters() {
    return [
        [
            'name' => 'bidderResourceId',
            'display' => 'Bidder resource ID',
            'required' => true
        ],
        [
            'name' => 'accountResourceId',
            'display' => 'Account resource ID',
            'required' => true
        ],
        [
            'name' => 'resourceId',
            'display' => 'Filterset resource ID',
            'required' => true
        ],
        [
            'name' => 'startDate',
            'display' => 'Start date (YYYYMMDD)',
            'required' => false
        ],
        [
            'name' => 'endDate',
            'display' => 'End date (YYYYMMDD)',
            'required' => false
        ],
        [
            'name' => 'creativeId',
            'display' => 'Creative ID',
            'required' => false
        ],
        [
            'name' => 'dealId',
            'display' => 'Deal ID',
            'required' => false
        ],
        [
            'name' => 'environment',
            'display' => 'Environment',
            'required' => false
        ],
        [
            'name' => 'format',
            'display' => 'Format',
            'required' => false
        ],
        [
            'name' => 'platforms',
            'display' => 'Platforms',
            'required' => false
        ],
        [
            'name' => 'sellerNetworkIds',
            'display' => 'Seller network IDs',
            'required' => false
        ],
        [
            'name' => 'timeSeriesGranularity',
            'display' => 'Time series granularity',
            'required' => false
        ],
        [
            'name' => 'isTransient',
            'display' => 'Is transient (true/false)',
            'required' => false
        ]
    ];
  }

  /**
   * @see BaseExample::run()
   */
  public function run() {
    $values = $this->formValues;
    $ownerName = sprintf(
        'bidders/%s/accounts/%s',
        $values['bidderResourceId'],
        $values['accountResourceId']
    );
    $name = sprintf(
        'bidders/%s/accounts/%s/filterSets/%s',
        $values['bidderResourceId'],
        $values['accountResourceId'],
        $values['resourceId']
    );

    print sprintf(
        '<h2>Creating Account-level filter set for ownerName %s</h2>',
        $ownerName
    );

    $filterSet = new Google_Service_AdExchangeBuyerII_FilterSet();
    $filterSet->name = $name;

    $absoluteDateRange = $this->getAbsoluteDateRange($values['startDate'],
        $values['endDate']);

    $filterSet->setAbsoluteDateRange($absoluteDateRange);

    $creativeId = $values['creativeId'];
    if (!is_null($creativeId)) {
      $filterSet->creativeId = $creativeId;
    }
    $dealId = $values['dealId'];
    if (!is_null($dealId)) {
      $filterSet->dealId = $dealId;
    }
    $environment = $values['environment'];
    if (!is_null($environment)) {
      $filterSet->environment = $environment;
    }
    $format = $values['format'];
    if (!is_null($format)) {
      $filterSet->format = $format;
    }
    $platforms = $values['platforms'];
    if (!is_null($platforms)) {
      $filterSet->platforms = explode(',', $platforms);
    }
    $sellerNetworkIds = $values['sellerNetworkIds'];
    if (!is_null($sellerNetworkIds)) {
      $filterSet->sellerNetworkIds = explode(',', $sellerNetworkIds);
    }
    $timeSeriesGranularity = $values['timeSeriesGranularity'];
    if (!is_null($timeSeriesGranularity)) {
      $filterSet->timeSeriesGranularity = $timeSeriesGranularity;
    }

    $queryParams = [
        'isTransient' => $values['isTransient'] === 'true'
    ];

    $result = $this->service->bidders_accounts_filterSets->create(
        $ownerName, $filterSet, $queryParams);
    $this->printResult($result);
  }

  /**
   * @see BaseExample::getClientType()
   */
  public function getClientType() {
    return ClientType::AdExchangeBuyerII;
  }

  /**
   * @see BaseExample::getName()
   */
  public function getName() {
    return 'RTBTroubleshooting: Create Account-level Filter Set';
  }
}

