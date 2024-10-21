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

		$user = $this->entityManager->getRepository($userClass)->find($id);
		$fullName = $user->getFullName();
		$builder
			->add('FullName', TextType::class, [
				'label' => false,
				'data' => $fullName,
				'attr' => [
					'placeholder' => 'Enter Full Name',
				],
			])
			->add('LastName', TextType::class, [
				'label' => false,
				'data' => $fullName,
				'attr' => [
					'placeholder' => 'Enter Full Name',
				],
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => null,
			'user_class' => null,
			'user_id' => null,
		]);
	}
}
