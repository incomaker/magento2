<?php

namespace Incomaker\Magento2\Helper;

use Incomaker\Api\Connector;
use Incomaker\Api\Data\Contact;
use Incomaker\Api\Data\Event;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Psr\Log\LoggerInterface;

class IncomakerApi {

	private CookieManagerInterface $cookieManager;
	private AddressRepositoryInterface $addressRepository;
	private CustomerRepositoryInterface $customerRepository;
	private Connector $incomaker;
	private LoggerInterface $logger;
	private $eventController;
	private $contactController;

	public function __construct(
		CookieManagerInterface $cookieManager,
		Connector $api,
		AddressRepositoryInterface $addressRepository,
		CustomerRepositoryInterface $customerRepository,
		LoggerInterface $logger
	) {
		$this->cookieManager = $cookieManager;
		$this->incomaker = $api;
		$this->addressRepository = $addressRepository;
		$this->customerRepository = $customerRepository;
		$this->logger = $logger;
	}

	public function getPermId() {
		if ($this->cookieManager->getCookie("incomaker_p") != null) {
			return $this->cookieManager->getCookie("incomaker_p");
		}
		return $this->cookieManager->getCookie("permId");
	}

	/**
	 * Checks whether plugin settings values are okay and logs a warning when not.
	 * @return boolean True if plugin settings are set up
	 */
	public function checkSettings() {
		if ($this->incomaker->isSettingsOk()) return true;
		$this->logger->warning("Incomaker plugin is not properly configured! Make sure you set values of API key, Account ID and Plugin ID on plugin configuration page.");
		return false;
	}

	public function getCampaignId() {
		return $this->cookieManager->getCookie("incomaker_c");
	}

	public function sendUserEvent($event, $customerId) {
		if (!$this->checkSettings()) return;

		$event = new Event($event, $this->getPermId());

		if (isset($customerId)) {
			$event->setClientContactId($customerId);
		}
		if (!isset($this->eventController)) {
			$this->eventController = $this->incomaker->createEventController();
		}
		$this->eventController->addEvent($event);
	}

	public function sendProductEvent($event, $customerId, $productId, $sessionId) {
		if (!$this->checkSettings()) return;

		$event = new Event($event, $this->getPermId());

		if (!empty($customerId)) {
			$event->setClientContactId($customerId);
		}
		if (!empty($productId)) {
			$event->setRelatedId($productId);
		}
		if (!empty($sessionId)) {
			$event->setSessionId($sessionId);
		}
		if (!isset($this->eventController)) {
			$this->eventController = $this->incomaker->createEventController();
		}
		$this->eventController->addEvent($event);
	}

	public function sendOrderEvent($event, $customerId, $total, $session) {
		if (!$this->checkSettings()) return;

		$event = new Event($event, $this->getPermId());

		if (isset($customerId)) {
			$event->setClientContactId($customerId);
		}
		$event->setCampaignId($this->getCampaignId());  //TODO remove passing campaignId this way
		$event->addCustomField("total", $total);
		if (!empty($session)) {
			$event->setSessionId($session);
		}
		if (!isset($this->eventController)) {
			$this->eventController = $this->incomaker->createEventController();
		}
		$this->eventController->addEvent($event);
	}

	public function sendAddContactEvent($customerId) {
		if (!$this->checkSettings()) return;

		if (!isset($this->contactController)) {
			$this->contactController = $this->incomaker->createContactController();
		}

		$customer = $this->customerRepository->getById($customerId);
		$contact = new Contact($customer->getId());
		$contact->setPermId($this->getPermId());
		$contact->setFirstName(htmlspecialchars($customer->getFirstname()));
		$contact->setLastName(htmlspecialchars($customer->getLastname()));
		$contact->setEmail($customer->getEmail());
		$contact->setBirthday($customer->getDob());

		$customerId = $customer->getDefaultBilling();
		if (isset($customerId)) {
			$billingAddress = $this->addressRepository->getById($customerId);
			if ($billingAddress != false) {
				$contact->setCompanyName(htmlspecialchars($billingAddress->getCompany()));
				if (isset($billingAddress->getStreet()[0])) {
					$contact->setStreet(htmlspecialchars($billingAddress->getStreet()[0]));
				}

				$contact->setZipCode(htmlspecialchars($billingAddress->getPostcode()));
				$contact->setCity(htmlspecialchars($billingAddress->getCity()));
				$contact->setPhoneNumber1($billingAddress->getTelephone());
				$contact->setPhoneNumber2($billingAddress->getFax());
				$contact->setCountry(strtolower($billingAddress->getCountryId()));
			}
		}

		$this->contactController->addContact($contact);
	}
}
