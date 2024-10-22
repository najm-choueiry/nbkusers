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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Intl\Countries;
use Rinvex\Country\CountryLoader;



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


		$user = $this->entityManager->getRepository($userClass)->find($id);
		$Address = $this->entityManager->getRepository($address_class)->findOneBy(['user_id'=>$id]);
		$WorkDetails = $this->entityManager->getRepository($WorkDetails_class)->findOneBy(['user_id'=>$id]);
		$broDetails = $this->entityManager->getRepository($broDetails_class)->findOneBy(['user_id'=>$id]);
		$politicalPos = $this->entityManager->getRepository($PoliticalPosition_class)->findOneBy(['user_id'=>$id]);


		$fullName = $user->getFullName();
		$MobileNumb = $user->getMobileNumb();
		$Email = $user->getEmail();
		$BranchUnit = $user->getBranchUnit();
		$MotherName = $user->getMothersName();
		$Gender = $user->getGender();
		$Dob = $user->getDob();
		$PlaceOfBirth = $user->getPlaceOfBirth();
		$Nationality = $user->getNationality();
		$CountryofOrigin = $user->getCountryOfOrigin();
		$countryNames = [];
		foreach (Countries::getNames('en') as $code => $name) {
			$countryNames[$name] = $name;
		}
		$NationalID=$user->getNationalId();
		$ExpirationNationalIDDate=$user->getExpirationDateNationalId();
		$CountryofOrigin = 'United States';

		$RegisterPlaceNo=$user->getRegisterPlaceNo();
		$countryCode = strtolower(array_search($CountryofOrigin, Countries::getNames('en')));
		$country = CountryLoader::country($countryCode);
		$countryArray = $country->getAttributes();

		// $regions = $countryArray['iso_3166_1_alpha2'];
		$regionChoices = [];
		// foreach ($regions  as $region) {
		//    $regionChoices[$region['name']] = $region['name'];
		// }


		$RegisterNumber=$user->getRegisterNumber();
		$MaritalStatus=$user->getMaritalStatus();
		$PassportNumber=$user->getPassportNumber();
		$PlaceofIssuePassport=$user->getPlaceOfIssuePassport();
		$ExpirationDatePassport=$user->getExpirationDatePassport();
		$OtherNationalities=$user->getOtherNationalities();
		$StatusinLebanon=$user->getStatusInLebanon();
		$NoofChildren=$user->getNoOfChildren();
	    $SpouseProfession=$user->getSpouseProfession();
		$SpouseName=$user->getSpouseName();
		$city=$Address->getCity();
		$street=$Address->getStreet();
		$building=$Address->getBuilding();
		$floor=$Address->getFloor();
		$apartment=$Address->getApartment();
		$HouseTelNO=$Address->getHouseTelephoneNumber();
		$InternationalAddress=$Address->getInternationalAddress();
		$profession=$WorkDetails->getProfession();
		$entityName=$WorkDetails->getEntityName();
		$activitySector=$WorkDetails->getActivitySector();
		$jobTitle=$WorkDetails->getJobTitle();
		$educationLevel=$WorkDetails->getEducationLevel();
		$WorkAddress=$WorkDetails->getWorkAddress();
		$WorkTelNo=$WorkDetails->getWorkTelephoneNumber();
		$ISListed=$WorkDetails->getPlaceOfWorkListed();
		$grade=$WorkDetails->getGrade();
        //BRO
		$CustomerSameAsBeneficiary=$broDetails->getCustomerSameAsBeneficiary();
		$broNationality=$broDetails->getBroNationality();
		$BeneficiaryName=$broDetails->getBeneficiaryName();
		$relationship=$broDetails->getRelationship();
		$broCivilIdNumber=$broDetails->getBroCivilIdNumber();
		$broexpirationDate=$broDetails->getExpirationDate();
		$reasonOfBro=$broDetails->getReasonOfBro();
		$broaddress=$broDetails->getAddress();
		$broprofession=$broDetails->getProfession();
		$incomeWealthDetails=$broDetails->getIncomeWealthDetails();
       //pol
		$politicalPosition=$politicalPos->getPoliticalPosition();
		$currentPrevious=$politicalPos->getCurrentPrevious();
		$pepname=$politicalPos->getPepName();
		$peprelationship=$politicalPos->getRelationship();
		$pepposition=$politicalPos->getPepPosition();

		//fina

        




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
			->add('BranchUnit', ChoiceType::class, [
				'label' => false,
				'data' => $BranchUnit,
				'attr' => [
					'placeholder' => 'Enter Branch Unit',
					'class' => 'form-control'
				],
				'choices' => [
					'privatebank' => 'privatebank',
					'bhamdoun' => 'bhamdoun',
					'sanayeh' => 'sanayeh',
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
				'choices' =>$countryNames, 
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
			->add('RegisterNumber', TextType::class, [
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
					'class' => 'form-control'
				],
				'choices' => $countryNames, 
				'multiple' => true, 
				'required' => false, 

			
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
			->add('InternationalAddress', TextType::class, [
				'label' => false,
				'data' => $InternationalAddress,
				'attr' => [
					'placeholder' => 'Enter International Address.',
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
					'Yes' =>true, 
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
					'Current' => 'Current',
					'Previous' => 'Previous',
				],
				'required' => false, 
				'placeholder' => 'Select  PEP position'
			])
			
			;
			

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
			'user_id' => null,
		]);
	}
	
}

