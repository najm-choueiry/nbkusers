<?php
// src/Form/QuestionType.php
namespace App\Form;

use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Intl\Countries;
use Rinvex\Country\CountryLoader;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class EditType extends AbstractType
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$id = $options['user_id'];
		$userClass = $options['user_class'];
		$address_class = $options['address_class'];
		$WorkDetails_class = $options['WorkDetails_class'];
		$broDetails_class = $options['broDetails_class'];
		$PoliticalPosition_class = $options['PoliticalPosition_class'];
		$FinancialDetails_class = $options['FinancialDetails_class'];

		$user = $this->entityManager->getRepository($userClass)->find($id);
		$Address = $this->entityManager->getRepository($address_class)->findOneBy(['user_id' => $id]);
		$WorkDetails = $this->entityManager->getRepository($WorkDetails_class)->findOneBy(['user_id' => $id]);
		$broDetails = $this->entityManager->getRepository($broDetails_class)->findOneBy(['user_id' => $id]);
		$politicalPos = $this->entityManager->getRepository($PoliticalPosition_class)->findOneBy(['user_id' => $id]);
		$FinancialDet = $this->entityManager->getRepository($FinancialDetails_class)->findOneBy(['user_id' => $id]);

		$fullName = $user->getFullName();
		$MobileNumb = $user->getMobileNumb();
		$Email = $user->getEmail();
		$BranchId = $user->getBranchId();
		

		$MotherName = $user->getMothersName() ?  $user->getMothersName() : null; 
		$Gender = $user->getGender();
		$Dob = $user->getDob();
		$PlaceOfBirth = $user->getPlaceOfBirth();
		$Nationality = $user->getNationality();
		$CountryofOrigin = $user->getCountryOfOrigin();
		$countryNames = [];
		foreach (Countries::getNames('en') as $code => $name) {
			$countryNames[$name] = $name;
		}
		$NationalID = $user->getNationalId();
		$ExpirationNationalIDDate = $user->getExpirationDateNationalId();
		$RegisterPlaceNo = $user->getRegisterPlaceNo();
		$countryCode = strtolower(array_search($CountryofOrigin, Countries::getNames('en')));
		if ($countryCode) {
			// Only load the country if $countryCode is valid
			$country = CountryLoader::country($countryCode);
		} else {
			// Default to null or handle as needed if the country is not found
			$country = null;
		}
		
		$countryArray = $country ? $country->getAttributes() : []; // Set to empty array if $country is null or not found
		
		$regionChoices = [];
		$RegisterNumber = $user->getRegisterNumber();
		$MaritalStatus = $user->getMaritalStatus();
		$PassportNumber = $user->getPassportNumber();
		$PlaceofIssuePassport = $user->getPlaceOfIssuePassport();
		$ExpirationDatePassport = $user->getExpirationDatePassport();
		$OtherNationalities = $user->getOtherNationalities();
		$StatusinLebanon = $user->getStatusInLebanon();
		$NoofChildren = $user->getNoOfChildren();
		$SpouseProfession = $user->getSpouseProfession();
		$SpouseName = $user->getSpouseName();
		$SpouseName = $user->getSpouseName();
		$otherCountriesTaxResidence = $user->getOtherCountriesTaxResidence();
		$TaxResidencyIdNumber = $user->getTaxResidencyIdNumber();
		//address
		$city = $Address ? $Address->getCity() : null;
		$street = $Address ?  $Address->getStreet() : null;
		$building = $Address ? $Address->getBuilding()  : null;
		$floor = $Address ? $Address->getFloor()  : null;
		$apartment = $Address ? $Address->getApartment()  : null;
		$HouseTelNO = $Address ? $Address->getHouseTelephoneNumber()  : null;
		$InternationalAddress = $Address ? $Address->getInternationalAddress()  : null;
		$intArea = $Address ? $Address->getIntArea()  : null;
		$intStreet = $Address ? $Address->getIntStreet()  : null;
		$intBuilding = $Address ? $Address->getIntBuilding()  : null;
		$intFloor = $Address ? $Address->getIntFloor()  : null;
		$intAppartment = $Address ? $Address->getIntApartment()  : null;
		$internationalHouseTelephoneNumber = $Address ? $Address->getInternationalHouseTelephoneNumber() : null ;
		$internationalMobileNumber = $Address ? $Address->getInternationalMobileNumber() : null;
		$alternateContactName = $Address ? $Address->getAlternateContactName() : null;
		$alternateTelephoneNumber = $Address ? $Address->getAlternateTelephoneNumber() : null;
		//workdeatils
		$profession =  $WorkDetails ?   $WorkDetails->getProfession(): null;
		$entityName =  $WorkDetails ? $WorkDetails->getEntityName(): null;
		$activitySector =  $WorkDetails ? $WorkDetails->getActivitySector(): null;
		$jobTitle =  $WorkDetails ? $WorkDetails->getJobTitle(): null;
		$educationLevel =  $WorkDetails ? $WorkDetails->getEducationLevel(): null;
		$WorkAddress =  $WorkDetails ? $WorkDetails->getWorkAddress(): null;
		$WorkTelNo =  $WorkDetails ? $WorkDetails->getWorkTelephoneNumber(): null;
		$ISListed =  $WorkDetails ? $WorkDetails->getPlaceOfWorkListed(): null;
		$grade =  $WorkDetails ? $WorkDetails->getGrade(): null;
		$publicSector =  $WorkDetails ? $WorkDetails->getPublicSector(): null;
		//BRO
		$CustomerSameAsBeneficiary =  $broDetails ?  $broDetails->getCustomerSameAsBeneficiary() : null;
		$broNationality = $broDetails ? $broDetails->getBroNationality() : null;
		$BeneficiaryName = $broDetails ? $broDetails->getBeneficiaryName() : null;
		$relationship = $broDetails ? $broDetails->getRelationship() : null;
		$broCivilIdNumber = $broDetails ? $broDetails->getBroCivilIdNumber() : null;
		$broexpirationDate = $broDetails ? $broDetails->getExpirationDate() : null;
		$reasonOfBro = $broDetails ? $broDetails->getReasonOfBro() : null;
		$broaddress = $broDetails ? $broDetails->getAddress() : null;
		$broprofession = $broDetails ? $broDetails->getProfession() : null;
		$incomeWealthDetails = $broDetails ? $broDetails->getIncomeWealthDetails() : null;
		//pol
		$politicalPosition =$politicalPos ? $politicalPos->getPoliticalPosition() :null;
		$currentPrevious = $politicalPos ? $politicalPos->getCurrentPrevious() :null;
		$yearOfRetirement = $politicalPos ? $politicalPos->getYearOfRetirement() :null;
		$pepname = $politicalPos ? $politicalPos->getPepName() :null;
		$peprelationship = $politicalPos ? $politicalPos->getRelationship() :null;
		$pepposition = $politicalPos ? $politicalPos->getPepPosition() :null;
		//fina
		$sourceOfFunds = $FinancialDet ?  $FinancialDet->getSourceOfFunds() :null;
		$currencyCodes = Currencies::getCurrencyCodes();
		$financelcurrency = $FinancialDet ? $FinancialDet->getCurrency() :null;
		$currencies = [];
		foreach ($currencyCodes as $currencyCode) {
			$currencies[$currencyCode] = $currencyCode;
		}
		$monthlyBasicSalary = $FinancialDet ? $FinancialDet->getMonthlyBasicSalary() :null;
		$monthlyAllowances = $FinancialDet ? $FinancialDet->getMonthlyAllowances():null;
		$AdditionalIncomeSourcesArray = $FinancialDet ? $FinancialDet->getAdditionalIncomeSourcesArray():null;
		$othersSourceOfFound = $FinancialDet ? $FinancialDet->getOthersSourceOfFound():null;
		$TotalEstimatedMonthlyIncome = $FinancialDet ? $FinancialDet->getTotalEstimatedMonthlyIncome():null;
		$estimatedWealthAmount = $FinancialDet ? $FinancialDet->getEstimatedWealthAmount():null;

		if ($TotalEstimatedMonthlyIncome <= 2000) {
			$IncomeCategory = 'micro';
		} elseif ($TotalEstimatedMonthlyIncome > 2000 && $TotalEstimatedMonthlyIncome <= 5000) {
			$IncomeCategory = 'small';
		} elseif ($TotalEstimatedMonthlyIncome > 5000 && $TotalEstimatedMonthlyIncome <= 15000) {
			$IncomeCategory = 'medium';
		} else {
			$IncomeCategory = 'large';
		}
		$isWealthInherited = $FinancialDet ? $FinancialDet->getIsWealthInherited():null;
		$sourcesOfWealth = $FinancialDet ? $FinancialDet->getSourcesOfWealth():null;
		$expectedValueOfTransactions = $FinancialDet ? $FinancialDet->getExpectedValueOfTransactions():null;
		$expectedNumberOfTransactions = $FinancialDet ? $FinancialDet->getExpectedNumberOfTransactions():null;
		$finanfrequency = $FinancialDet ? $FinancialDet->getFrequency():null;
		$otherAccountsAtBanks = $FinancialDet ? $FinancialDet->getHasOtherAccounts():null;
		$bankNameone = $FinancialDet ? $FinancialDet->getBankName():null;
		$Countryone = $FinancialDet ? $FinancialDet->getCountry():null;
		$AccountBalanceOne = $FinancialDet ? $FinancialDet->getAccountBalance():null;
		$secondBankName = $FinancialDet ? $FinancialDet->getSecondBankName():null;
		$secondCountry = $FinancialDet ? $FinancialDet->getSecondCountry():null;
		$secondBankBalance = $FinancialDet ? $FinancialDet->getSecondBankBalance():null;
		$thirdBankName = $FinancialDet ? $FinancialDet->getThirdBankName():null;
		$thirdAccountCountry = $FinancialDet ? $FinancialDet->getThirdAccountCountry():null;
		$thirdAccountBalance = $FinancialDet ? $FinancialDet->getThirdAccountBalance():null;
		$natureOfRelation = $FinancialDet ? $FinancialDet->getNatureOfRelation():null;
		$purposeOfRelation = $FinancialDet ? $FinancialDet->getPurposeOfRelation():null;
		$selectIDType = $FinancialDet ? $FinancialDet->getSelectIDType():null;
		$frontImageID = $FinancialDet ? $FinancialDet->getFrontImageID():null;
		$backImageID = $FinancialDet ? $FinancialDet->getBackImageID():null;
		$realEstateTitle = $FinancialDet ? $FinancialDet->getRealEstateTitle():null;
		$accountStatement = $FinancialDet ? $FinancialDet->getAccountStatement():null;
		$otherDocument = $FinancialDet ? $FinancialDet->getOtherDocument():null;
		$employerLetter = $FinancialDet ? $FinancialDet->getEmployerLetter() :null;
		if ($MotherName)
		{

		$builder
			->add('FullName', TextType::class, [
				'label' => false,
				'data' => $fullName,
				'attr' => [
					'placeholder' => 'Enter Full Name',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('MobileNumb', TextType::class, [
				'label' => false,
				'data' => $MobileNumb,
				'attr' => [
					'placeholder' => 'Enter Mobile Numb',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('Email', TextType::class, [
				'label' => false,
				'data' => $Email,
				'attr' => [
					'placeholder' => 'Enter Email',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('BranchId', ChoiceType::class, [
				'label' => false,
				'data' => $BranchId,
				'attr' => [
					'placeholder' => 'Enter Branch Unit',
					'class' => 'form-control',
				],
				'choices' => [
					'sanayeh' => 1,
					'bhamdoun' => 2,
					'privatebank' => 3,
				],
				'required' => false,
			])


			->add('MotherName', TextType::class, [
				'label' => false,
				'data' => $MotherName,
				'attr' => [
					'placeholder' => 'Enter Mother Name',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('Gender', ChoiceType::class, [
				'label' => false,
				'data' => $Gender,
				'attr' => [
					'placeholder' => 'Enter Gender',
					'class' => 'form-control'
				],
				'choices' => [
					'female' => 'female',
					'male' => 'male',
				],
				'required' => false,
			])
			->add('Dob', DateType::class, [
				'label' => false,
				'data' => $Dob,
				'widget' => 'single_text',
				'attr' => [
					'placeholder' => 'Enter Date of Birth',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('PlaceOfBirth', TextType::class, [
				'label' => false,
				'data' => $PlaceOfBirth,
				'attr' => [
					'placeholder' => 'Enter Place of Birth',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('CountryofOrigin', ChoiceType::class, [
				'label' => false,
				'data' => $CountryofOrigin,
				'attr' => [
					'placeholder' => 'Enter Country of Origin',
					'class' => 'form-control'
				],
				'choices' => $countryNames,
				'required' => false,
			])
			->add('Nationality', ChoiceType::class, [
				'label' => false,
				'data' => $Nationality,
				'attr' => [
					'placeholder' => 'Enter Nationality',
					'class' => 'form-control'
				],
				'choices' =>  $countryNames,
				'required' => false,
			])
			->add('NationalID', TextType::class, [
				'label' => false,
				'data' => $NationalID,
				'attr' => [
					'placeholder' => 'Enter National ID',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('ExpirationNationalIDDate', DateType::class, [
				'label' => false,
				'data' => $ExpirationNationalIDDate,
				'widget' => 'single_text',
				'attr' => [
					'placeholder' => 'Enter Expiration Date (National ID)',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('RegisterPlaceNo', TextType::class, [
				'label' => false,
				'data' => $RegisterPlaceNo,
				'attr' => [
					'placeholder' => 'Enter Register Place No',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('RegisterNumber', IntegerType::class, [
				'label' => false,
				'data' => $RegisterNumber,
				'attr' => [
					'placeholder' => 'Enter Register Number',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('MaritalStatus', ChoiceType::class, [
				'label' => false,
				'data' => $MaritalStatus,
				'attr' => [
					'placeholder' => 'Enter Marital Status',
					'class' => 'form-control'
				],
				'choices' => [
					'married' => 'married',
					'single' => 'single',
					'divorced' => 'divorced',
					'widow' => 'widow',
				],
				'required' => false,
			])
			->add('PassportNumber', TextType::class, [
				'label' => false,
				'data' => $PassportNumber,
				'attr' => [
					'placeholder' => 'Enter Passport Number',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('PlaceofIssuePassport', TextType::class, [
				'label' => false,
				'data' => $PlaceofIssuePassport,
				'attr' => [
					'placeholder' => 'Enter Place of Issue Passport',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('ExpirationDatePassport', DateType::class, [
				'label' => false,
				'data' => $ExpirationDatePassport,
				'widget' => 'single_text',
				'attr' => [
					'placeholder' => 'Enter Expiration Date (Passport)',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('OtherNationalities', ChoiceType::class, [
				'label' => false,
				'data' => $OtherNationalities,
				'attr' => [
					'placeholder' => 'Enter Other Nationalities',
					'class' => ' custom-checkbox-dropdown'
				],
				'choices' => $countryNames,
				'multiple' => true,
				'required' => false,
				'expanded' => true,
			])
			->add('StatusinLebanon', ChoiceType::class, [
				'label' => false,
				'data' => $StatusinLebanon,
				'attr' => [
					'placeholder' => 'Enter Status in Lebanon',
					'class' => 'form-control'
				],
				'choices' => [
					'resident' => 'resident',
					'nonresident' => 'nonresident',
				],
				'required' => false,
			])
			->add('otherCountriesTaxResidence', ChoiceType::class, [
				'label' => false,
				'data' => $otherCountriesTaxResidence,
				'attr' => [
					'placeholder' => 'Enter other Countries Tax Residence',
					'class' => 'form-control'
				],
				'choices' =>  $countryNames,
				'required' => false,
			])
			->add('TaxResidencyIdNumber', TextType::class, [
				'label' => false,
				'data' => $TaxResidencyIdNumber,
				'attr' => [
					'placeholder' => 'Enter Tax Residency Id Number',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('InternationalAddress', ChoiceType::class, [
				'label' => false,
				'data' => $InternationalAddress,
				'attr' => [
					'placeholder' => 'Enter International Address.',
					'class' => 'form-control'
				],
				'required' => false,
				'choices' =>  $countryNames,
			])
			->add('intArea', TextType::class, [
				'label' => false,
				'data' => $intArea,
				'attr' => [
					'placeholder' => 'Enter International Area',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('intStreet', TextType::class, [
				'label' => false,
				'data' => $intStreet,
				'attr' => [
					'placeholder' => 'Enter International Street',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('intBuilding', TextType::class, [
				'label' => false,
				'data' => $intBuilding,
				'attr' => [
					'placeholder' => 'Enter  International Building/House',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('intFloor', TextType::class, [
				'label' => false,
				'data' => $intFloor,
				'attr' => [
					'placeholder' => 'Enter  International Floor',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('intAppartment', TextType::class, [
				'label' => false,
				'data' => $intAppartment,
				'attr' => [
					'placeholder' => 'Enter International Appartment',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('internationalHouseTelephoneNumber', TextType::class, [
				'label' => false,
				'data' => $internationalHouseTelephoneNumber,
				'attr' => [
					'placeholder' => 'Enter International House Telephone Number',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('internationalMobileNumber', TextType::class, [
				'label' => false,
				'data' => $internationalMobileNumber,
				'attr' => [
					'placeholder' => 'Enter International Mobile Number',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('alternateContactName', TextType::class, [
				'label' => false,
				'data' => $alternateContactName,
				'attr' => [
					'placeholder' => 'Enter alternate Contact Name',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('alternateTelephoneNumber', TextType::class, [
				'label' => false,
				'data' => $alternateTelephoneNumber,
				'attr' => [
					'placeholder' => 'Enter alternate Tele phone Number',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('publicSector', ChoiceType::class, [
				'label' => false,
				'data' => $publicSector,
				'attr' => [
					'placeholder' => 'Enter public Sector',
					'class' => 'form-control'
				],
				'required' => false,
				'choices' => [
					'yes' => 'yes',
					'no' => 'no',
				],
			])
			->add('SpouseName', TextType::class, [
				'label' => false,
				'data' => $SpouseName,
				'attr' => [
					'placeholder' => 'Enter Spouse Name',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('SpouseProfession', TextType::class, [
				'label' => false,
				'data' => $SpouseProfession,
				'attr' => [
					'placeholder' => 'Enter Spouse Profession',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('NoofChildren', ChoiceType::class, [
				'label' => false,
				'data' => $NoofChildren,
				'attr' => [
					'placeholder' => 'Enter No of Children',
					'class' => 'form-control'
				],
				'choices' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
				],
				'required' => false,
			])
			->add('city', TextType::class, [
				'label' => false,
				'data' => $city,
				'attr' => [
					'placeholder' => 'Enter city',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('street', TextType::class, [
				'label' => false,
				'data' => $street,
				'attr' => [
					'placeholder' => 'Enter street',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('building', TextType::class, [
				'label' => false,
				'data' => $building,
				'attr' => [
					'placeholder' => 'Enter building',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('floor', TextType::class, [
				'label' => false,
				'data' => $floor,
				'attr' => [
					'placeholder' => 'Enter floor',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('apartment', TextType::class, [
				'label' => false,
				'data' => $apartment,
				'attr' => [
					'placeholder' => 'Enter apartment',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('HouseTelNO', TextType::class, [
				'label' => false,
				'data' => $HouseTelNO,
				'attr' => [
					'placeholder' => 'Enter House Tel NO.',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('profession', TextType::class, [
				'label' => false,
				'data' => $profession,
				'attr' => [
					'placeholder' => 'Enter profession.',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('jobTitle', TextType::class, [
				'label' => false,
				'data' => $jobTitle,
				'attr' => [
					'placeholder' => 'Enter jobTitle.',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('activitySector', TextType::class, [
				'label' => false,
				'data' => $activitySector,
				'attr' => [
					'placeholder' => 'Enter activitySector.',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('entityName', TextType::class, [
				'label' => false,
				'data' => $entityName,
				'attr' => [
					'placeholder' => 'Enter entity Name.',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('educationLevel', TextType::class, [
				'label' => false,
				'data' => $educationLevel,
				'attr' => [
					'placeholder' => 'Enter Education Level',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('WorkAddress', TextType::class, [
				'label' => false,
				'data' => $WorkAddress,
				'attr' => [
					'placeholder' => 'Enter Work Address',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('WorkTelNo', TextType::class, [
				'label' => false,
				'data' => $WorkTelNo,
				'attr' => [
					'placeholder' => 'Enter Work Tel No',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('ISListed', ChoiceType::class, [
				'label' => false,
				'data' => $ISListed,
				'attr' => [
					'placeholder' => 'Enter IS Listed',
					'class' => 'form-control'
				],
				'choices' => [
					'Yes' => true,
					'No' => false,
				],
				'required' => false,
			])
			->add('grade', TextType::class, [
				'label' => false,
				'data' => $grade,
				'attr' => [
					'placeholder' => 'Enter grade',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('CustomerSameAsBeneficiary', ChoiceType::class, [
				'label' => false,
				'data' => $CustomerSameAsBeneficiary,
				'attr' => [
					'placeholder' => 'Enter IS Customer Same As Beneficiary',
					'class' => 'form-control'
				],
				'choices' => [
					'Yes' => true,
					'No' => false,
				],
				'required' => false,
			])
			->add('broNationality', ChoiceType::class, [
				'label' => false,
				'data' => $broNationality,
				'attr' => [
					'placeholder' => 'Enter bro Nationality',
					'class' => 'form-control'
				],
				'choices' => $countryNames,
				'required' => false,
				'placeholder' => 'Select  bro Nationality',
			])
			->add('BeneficiaryName', TextType::class, [
				'label' => false,
				'data' => $BeneficiaryName,
				'attr' => [
					'placeholder' => 'Enter BeneficiaryName',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('relationship', TextType::class, [
				'label' => false,
				'data' => $relationship,
				'attr' => [
					'placeholder' => 'Enter Relationship',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('broCivilIdNumber', TextType::class, [
				'label' => false,
				'data' => $broCivilIdNumber,
				'attr' => [
					'placeholder' => 'Enter BRO Civil Id Number',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('broexpirationDate', DateType::class, [
				'label' => false,
				'data' => $broexpirationDate,
				'widget' => 'single_text',
				'attr' => [
					'placeholder' => 'Enter BRO Expiration Date',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('reasonOfBro', TextType::class, [
				'label' => false,
				'data' => $reasonOfBro,
				'attr' => [
					'placeholder' => 'Enter Reason Of BRO',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('broaddress', TextType::class, [
				'label' => false,
				'data' => $broaddress,
				'attr' => [
					'placeholder' => 'Enter BRO Address',
					'class' => 'form-control'
				],
				'required' => false,

			])
			->add('broprofession', TextType::class, [
				'label' => false,
				'data' => $broprofession,
				'attr' => [
					'placeholder' => 'Enter BRO profession',
					'class' => 'form-control'
				],
				'required' => false,

			])
			->add('incomeWealthDetails', TextType::class, [
				'label' => false,
				'data' => $incomeWealthDetails,
				'attr' => [
					'placeholder' => 'Enter BRO Income Wealth Details',
					'class' => 'form-control'
				],
				'required' => false,

			])
			->add('politicalPosition', ChoiceType::class, [
				'label' => false,
				'data' => $politicalPosition,
				'attr' => [
					'placeholder' => 'Enter Political Position',
					'class' => 'form-control'
				],
				'choices' => [
					'Yes' => true,
					'No' => false,
				],
			])
			->add('currentPrevious', ChoiceType::class, [
				'label' => false,
				'data' => $currentPrevious,
				'attr' => [
					'placeholder' => 'Enter Current Previous',
					'class' => 'form-control'
				],
				'choices' => [
					'Current' => 'Current',
					'Previous' => 'Previous',
				],
				'required' => false,
				'placeholder' => 'Select  bro Nationality'
			])
			->add('yearOfRetirement', TextType::class, [
				'label' => false,
				'data' => $yearOfRetirement,
				'attr' => [
					'placeholder' => 'Enter year Of Retirement',
					'class' => 'form-control'
				],
				'required' => false,

			])
			->add('pepname', TextType::class, [
				'label' => false,
				'data' => $pepname,
				'attr' => [
					'placeholder' => 'Enter PEP Name',
					'class' => 'form-control'
				],
				'required' => false,

			])
			->add('peprelationship', TextType::class, [
				'label' => false,
				'data' => $peprelationship,
				'attr' => [
					'placeholder' => 'Enter PEP Relation Ship',
					'class' => 'form-control'
				],
				'required' => false,

			])
			->add('pepposition', ChoiceType::class, [
				'label' => false,
				'data' => $pepposition,
				'attr' => [
					'placeholder' => 'Enter PEP position',
					'class' => 'form-control'
				],
				'choices' => [
					'Royal Family' => 'royalfamily',
					'Member of Parliament' => 'memberofparliament',
					'Senior Military' => 'seniormilitary',
					'Senior Government Officer' => 'seniorgovernmentofficer',
					'Senior Politician' => 'seniorpolitician',
					'Entities Diplomat' => 'entitiesdiplomat',
					'Attorney General' => 'attorneygeneral',
					'Court President/Deputy' => 'courtpresidentdeputy',
					'Attorney General' => 'attorneygeneral',
					'Senior Position at Intl Organisation' => 'seniorpositionatIntlorganisation',
				],
				'required' => false,
				'placeholder' => 'Select  PEP position'
			])
			->add('sourceOfFunds', TextType::class, [
				'label' => false,
				'data' => $sourceOfFunds,
				'attr' => [
					'placeholder' => 'Enter sourceOfFunds',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('financelcurrency', ChoiceType::class, [
				'label' => false,
				'data' => $financelcurrency,
				'attr' => [
					'placeholder' => 'Enter financel currency',
					'class' => 'form-control'
				],
				'choices' => array_flip($currencies),
				'required' => false,
				'placeholder' => 'Select  financel currency'
			])
			->add('monthlyBasicSalary', NumberType::class, [
				'label' => false,
				'data' => $monthlyBasicSalary,
				'attr' => [
					'placeholder' => 'Enter monthly Basic Salary',
					'class' => 'form-control',
					'step' => '0.01',
				],
				'required' => false,
			])
			->add('monthlyAllowances', NumberType::class, [
				'label' => false,
				'data' => $monthlyAllowances,
				'attr' => [
					'placeholder' => 'Enter monthly Allowances',
					'class' => 'form-control',
					'step' => '0.01',
				],
				'required' => false,
			])
			->add('AdditionalIncomeSourcesArray', ChoiceType::class, [
				'label' => false,
				'data' => $AdditionalIncomeSourcesArray,
				'attr' => [
					'class' => ' custom-checkbox-dropdown',
				],
				'choices' => [
					'Real Estate/Lands (Rent Income)' => 'real estate lands rent income',
					'Trading' => 'trading',
					'Investments' => 'investments',
					'Others' => 'others',
				],
				'multiple' => true,
				'required' => false,
				'expanded' => true, // renders as checkboxes
			])
			
			
			->add('othersSourceOfFound', TextType::class, [
				'label' => false,
				'data' => $othersSourceOfFound,
				'attr' => [
					'placeholder' => 'Enter others Source Of Found',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('TotalEstimatedMonthlyIncome', NumberType::class, [
				'label' => false,
				'data' => $TotalEstimatedMonthlyIncome,
				'attr' => [
					'placeholder' => 'Enter Total Estimated Monthly Income',
					'class' => 'form-control',
					'step' => '0.01',
				],
				'required' => false,
			])
			->add('estimatedWealthAmount', NumberType::class, [
				'label' => false,
				'data' => $estimatedWealthAmount,
				'attr' => [
					'placeholder' => 'Enter estimated Wealth Amount',
					'class' => 'form-control',
					'step' => '0.01',
				],
				'required' => false,
			])
			->add('IncomeCategory', ChoiceType::class, [
				'label' => false,
				'data' => $IncomeCategory,
				'attr' => [
					'placeholder' => 'Enter Income Category',
					'class' => 'form-control'
				],
				'choices' => [
					'Micro' => 'micro',
					'Small' => 'small',
					'Medium' => 'medium',
					'Large' => 'large',
				],
				'required' => false,
			])
			->add('isWealthInherited', ChoiceType::class, [
				'label' => false,
				'data' => $isWealthInherited,
				'attr' => [
					'placeholder' => 'Enter IS Listed',
					'class' => 'form-control'
				],
				'choices' => [
					'Yes' => true,
					'No' => false,
				],
				'required' => false,
			])
			->add('sourcesOfWealth', TextType::class, [
				'label' => false,
				'data' => $sourcesOfWealth,
				'attr' => [
					'placeholder' => 'Enter sources Of Wealth',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('expectedNumberOfTransactions', IntegerType::class, [
				'label' => false,
				'data' => $expectedNumberOfTransactions,
				'attr' => [
					'placeholder' => 'Enter expected Number Of Transactions',
					'class' => 'form-control',
				],
				'required' => false,
			])
			->add('expectedValueOfTransactions', NumberType::class, [
				'label' => false,
				'data' => $expectedValueOfTransactions,
				'attr' => [
					'placeholder' => 'Enter expected Value Of Transactions',
					'class' => 'form-control',
					'step' => '0.01',
				],
				'required' => false,
			])
			->add('finanfrequency', ChoiceType::class, [
				'label' => false,
				'data' => $finanfrequency,
				'attr' => [
					'placeholder' => 'Enter frequency',
					'class' => 'form-control'
				],
				'choices' => [
					'Monthly' => 'monthlyy',
					'Yearly' => 'yearly',
				],
				'required' => false,
			])
			->add('otherAccountsAtBanks', ChoiceType::class, [
				'label' => false,
				'data' => $otherAccountsAtBanks,
				'attr' => [
					'placeholder' => 'Enter Other Accounts AtBanks',
					'class' => 'form-control'
				],
				'choices' => [
					'Yes' => true,
					'No' => false,
				],
			])
			->add('bankNameone', TextType::class, [
				'label' => false,
				'data' => $bankNameone,
				'attr' => [
					'placeholder' => 'Enter bank Name',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('Countryone', ChoiceType::class, [
				'label' => false,
				'data' => $Countryone,
				'attr' => [
					'placeholder' => 'Enter   Country',
					'class' => 'form-control'
				],
				'choices' => $countryNames,
				'required' => false,
			])
			->add('AccountBalanceOne', NumberType::class, [
				'label' => false,
				'data' => $AccountBalanceOne,
				'attr' => [
					'placeholder' => 'Enter Account Balance ',
					'class' => 'form-control',
					'step' => '0.01',
				],
				'required' => false,
			])
			->add('secondBankName', TextType::class, [
				'label' => false,
				'data' => $secondBankName,
				'attr' => [
					'placeholder' => 'Enter second Bank Name',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('secondCountry', ChoiceType::class, [
				'label' => false,
				'data' => $secondCountry,
				'attr' => [
					'placeholder' => 'Enter  second Country',
					'class' => 'form-control'
				],
				'choices' => $countryNames,
				'required' => false,
			])
			->add('secondBankBalance', NumberType::class, [
				'label' => false,
				'data' => $secondBankBalance,
				'attr' => [
					'placeholder' => 'Enter Second Account Balance ',
					'class' => 'form-control',
					'step' => '0.01',
				],
				'required' => false,
			])
			->add('thirdBankName', TextType::class, [
				'label' => false,
				'data' => $thirdBankName,
				'attr' => [
					'placeholder' => 'Enter third Bank Name',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('thirdAccountCountry', ChoiceType::class, [
				'label' => false,
				'data' => $thirdAccountCountry,
				'attr' => [
					'placeholder' => 'Enter  third Country',
					'class' => 'form-control'
				],
				'choices' => $countryNames,
				'required' => false,
			])
			->add('thirdAccountBalance', NumberType::class, [
				'label' => false,
				'data' => $thirdAccountBalance,
				'attr' => [
					'placeholder' => 'Enter third Account Balance',
					'class' => 'form-control',
					'step' => '0.01',
				],
				'required' => false,
			])
			->add('natureOfRelation', TextType::class, [
				'label' => false,
				'data' => $natureOfRelation,
				'attr' => [
					'placeholder' => 'Enter nature Of Relation',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('purposeOfRelation', TextType::class, [
				'label' => false,
				'data' => $purposeOfRelation,
				'attr' => [
					'placeholder' => 'Enter purpose Of Relation',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('selectIDType', ChoiceType::class, [
				'label' => false,
				'data' => $selectIDType,
				'attr' => [
					'placeholder' => 'Enter select ID Type',
					'class' => 'form-control'
				],
				'choices' => [
					'Pational ID' => 'nationalID',
					'Passport' => 'passport',
				],
				'required' => false,
			])
			->add('frontImageID', FileType::class, [
				'label' => 'Front Image (JPEG or PNG file)',
				'required' => false,
				'constraints' => [
					new File([
						'maxSize' => '5M',
						'mimeTypes' => [
							'image/jpeg',
							'image/png',
						],
						'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
					])
				],
				'attr' => [
					'class' => 'form-control',
					'accept' => 'image/jpeg,image/png'
				],
			])
			->add('backImageID', FileType::class, [
				'label' => 'back Image (JPEG or PNG file)',
				'required' => false,
				'constraints' => [
					new File([
						'maxSize' => '5M',
						'mimeTypes' => [
							'image/jpeg',
							'image/png',
						],
						'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
					])
				],
				'attr' => [
					'class' => 'form-control',
					'accept' => 'image/jpeg,image/png'
				],
			])
			->add('otherDocument', FileType::class, [
				'label' => 'other Document Image (JPEG or PNG file)',
				'required' => false,
				'constraints' => [
					new File([
						'maxSize' => '5M',
						'mimeTypes' => [
							'image/jpeg',
							'image/png',
						],
						'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
					])
				],
				'attr' => [
					'class' => 'form-control',
					'accept' => 'image/jpeg,image/png'
				],
			])
			->add('employerLetter', FileType::class, [
				'label' => 'employer Letter Image (JPEG or PNG file)',
				'required' => false,
				'constraints' => [
					new File([
						'maxSize' => '5M',
						'mimeTypes' => [
							'image/jpeg',
							'image/png',
						],
						'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
					])
				],
				'attr' => [
					'class' => 'form-control',
					'accept' => 'image/jpeg,image/png'
				],
			])
			->add('accountStatement', FileType::class, [
				'label' => 'account Statement Image (JPEG or PNG file)',
				'required' => false,
				'constraints' => [
					new File([
						'maxSize' => '5M',
						'mimeTypes' => [
							'image/jpeg',
							'image/png',
						],
						'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
					])
				],
				'attr' => [
					'class' => 'form-control',
					'accept' => 'image/jpeg,image/png'
				],
			])
			->add('realEstateTitle', FileType::class, [
				'label' => 'real Estate Title Image (JPEG or PNG file)',
				'required' => false,
				'constraints' => [
					new File([
						'maxSize' => '5M',
						'mimeTypes' => [
							'image/jpeg',
							'image/png',
						],
						'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
					])
				],
				'attr' => [
					'class' => 'form-control',
					'accept' => 'image/jpeg,image/png'
				],
			]);
		} else {
			$builder
			->add('FullName', TextType::class, [
				'label' => false,
				'data' => $fullName,
				'attr' => [
					'placeholder' => 'Enter Full Name',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('MobileNumb', TextType::class, [
				'label' => false,
				'data' => $MobileNumb,
				'attr' => [
					'placeholder' => 'Enter Mobile Numb',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('Email', TextType::class, [
				'label' => false,
				'data' => $Email,
				'attr' => [
					'placeholder' => 'Enter Email',
					'class' => 'form-control'
				],
				'required' => false,
			])
			->add('BranchId', ChoiceType::class, [
				'label' => false,
				'data' => $BranchId,
				'attr' => [
					'placeholder' => 'Enter Branch Unit',
					'class' => 'form-control',
				],
				'choices' => [
					'sanayeh' => 1,
					'bhamdoun' => 2,
					'privatebank' => 3,
				],
				'required' => false,
			]);

		}
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => null,
			'user_class' => null,
			'address_class' => null,
			'WorkDetails_class' => null,
			'broDetails_class' => null,
			'PoliticalPosition_class' => null,
			'FinancialDetails_class' => null,
			'user_id' => null,
		]);
	}
}
