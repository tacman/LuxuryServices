<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\ApplicationStatus;
use App\Entity\Candidate;
use App\Entity\Contact;
use App\Entity\ContactStatus;
use App\Entity\Customer;
use App\Entity\Experience;
use App\Entity\GenderList;
use App\Entity\JobCategory;
use App\Entity\JobOffer;
use App\Entity\JobType;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $categories = [];
        foreach (['Commercial', 'Creative', 'Marketing & PR', 'Technology', 'Fashion & luxury', 'Retail sales'] as $value) {
            $category = (new JobCategory())->setCategoryValue($value);
            $categories[$value] = $category;
            $manager->persist($category);
        }

        $jobTypes = [];
        foreach (['Permanent', 'Contract', 'Temporary'] as $value) {
            $jobType = (new JobType())->setTypeValue($value);
            $jobTypes[$value] = $jobType;
            $manager->persist($jobType);
        }

        $experiences = [];
        foreach (['Entry level', 'Mid level', 'Senior', 'Executive'] as $value) {
            $experience = (new Experience())->setExperienceValue($value);
            $experiences[$value] = $experience;
            $manager->persist($experience);
        }

        $genders = [];
        foreach (['Woman', 'Man', 'Non-binary', 'Prefer not to say'] as $value) {
            $gender = (new GenderList())->setGenderValue($value);
            $genders[$value] = $gender;
            $manager->persist($gender);
        }

        $applicationStatuses = [];
        foreach (['Pending', 'Interview', 'Accepted', 'Declined'] as $value) {
            $status = (new ApplicationStatus())->setStatusValue($value);
            $applicationStatuses[$value] = $status;
            $manager->persist($status);
        }

        $contactStatuses = [];
        foreach (['Pending', 'Processed', 'Declined'] as $value) {
            $status = (new ContactStatus())->setStatusValue($value);
            $contactStatuses[$value] = $status;
            $manager->persist($status);
        }

        $customers = [
            (new Customer())
                ->setCompanyName('Maison Aurelia')
                ->setActivityType('Luxury fashion')
                ->setContactName('Sophie Laurent')
                ->setPosition('Talent Director')
                ->setContactPhoneNumber('+33184001234')
                ->setContactEmail('sophie@aurelia.test')
                ->setCreatedAt(new \DateTimeImmutable('-180 days')),
            (new Customer())
                ->setCompanyName('Grand Horizon Hotels')
                ->setActivityType('Hospitality')
                ->setContactName('James Bennett')
                ->setPosition('People Partner')
                ->setContactPhoneNumber('+442079460321')
                ->setContactEmail('james@horizon.test')
                ->setCreatedAt(new \DateTimeImmutable('-120 days')),
        ];
        foreach ($customers as $customer) {
            $manager->persist($customer);
        }

        $offerData = [
            ['LUX-1001', 'Client Experience Manager', 'Commercial', 'Permanent', 'Paris, France', 72000, $customers[0]],
            ['LUX-1002', 'Senior Digital Art Director', 'Creative', 'Contract', 'London, United Kingdom', 88000, $customers[0]],
            ['LUX-1003', 'CRM & Loyalty Lead', 'Marketing & PR', 'Permanent', 'New York, United States', 105000, $customers[1]],
            ['LUX-1004', 'E-commerce Platform Engineer', 'Technology', 'Permanent', 'Remote / Europe', 98000, $customers[0]],
            ['LUX-1005', 'Guest Relations Specialist', 'Retail sales', 'Temporary', 'Monaco', 52000, $customers[1]],
            ['LUX-1006', 'Luxury Brand Merchandiser', 'Fashion & luxury', 'Contract', 'Milan, Italy', 68000, $customers[0]],
        ];

        $offers = [];
        foreach ($offerData as $index => [$reference, $title, $category, $type, $location, $salary, $customer]) {
            $offer = (new JobOffer())
                ->setJobTitle($title)
                ->setPosition($title)
                ->setJobCategory($categories[$category])
                ->setJobType($jobTypes[$type])
                ->setCustomer($customer)
                ->setLocation($location)
                ->setSalary($salary)
                ->setDescription(sprintf(
                    'Join %s and help deliver thoughtful, high-touch service. You will collaborate across teams, own key initiatives, and create memorable experiences for discerning clients.',
                    $customer->getCompanyName(),
                ))
                ->setClosingDate(new \DateTime(sprintf('+%d days', 21 + $index * 5)))
                ->setCreatedAt(new \DateTimeImmutable(sprintf('-%d days', 12 - $index)))
                ->setReference($reference)
                ->setIsActive(true);
            $offers[] = $offer;
            $manager->persist($offer);
        }

        $candidateData = [
            ['admin', ['ROLE_ADMIN'], 'Alex', 'Morgan', 'Technology', 'Executive', 'Prefer not to say'],
            ['candidate', [], 'Camille', 'Dubois', 'Creative', 'Mid level', 'Woman'],
            ['marcus', [], 'Marcus', 'Reed', 'Commercial', 'Senior', 'Man'],
        ];

        $candidates = [];
        foreach ($candidateData as $index => [$username, $roles, $firstName, $lastName, $category, $experience, $gender]) {
            $user = (new User())
                ->setEmail($username)
                ->setRoles($roles)
                ->setIsActive(true);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));

            $candidate = (new Candidate())
                ->setUser($user)
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setGender($genders[$gender])
                ->setAdress(sprintf('%d Avenue Montaigne', 10 + $index))
                ->setCountry($index === 2 ? 'United States' : 'France')
                ->setNationality($index === 2 ? 'American' : 'French')
                ->setIsPassportValid(true)
                ->setCurrentLocation($index === 2 ? 'New York' : 'Paris')
                ->setDateOfBirth(new \DateTime(sprintf('-%d years', 30 + $index * 4)))
                ->setPlaceOfBirth($index === 2 ? 'Chicago' : 'Lyon')
                ->setIsAvailable(true)
                ->setJobCategory($categories[$category])
                ->setExperience($experiences[$experience])
                ->setShortDescription('Luxury-sector professional focused on excellent service and lasting client relationships.')
                ->setCreatedAt(new \DateTimeImmutable(sprintf('-%d days', 60 + $index * 10)));
            $user->setCandidate($candidate);

            $candidates[] = $candidate;
            $manager->persist($user);
            $manager->persist($candidate);
        }

        foreach ([
            [$candidates[1], $offers[1], 'Interview', '-8 days'],
            [$candidates[1], $offers[3], 'Pending', '-3 days'],
            [$candidates[2], $offers[0], 'Accepted', '-14 days'],
            [$candidates[2], $offers[4], 'Pending', '-2 days'],
        ] as [$candidate, $offer, $status, $createdAt]) {
            $manager->persist(
                (new Application())
                    ->setCandidate($candidate)
                    ->setJobOffer($offer)
                    ->setStatus($applicationStatuses[$status])
                    ->setCreatedAt(new \DateTimeImmutable($createdAt)),
            );
        }

        foreach ([
            ['Elena', 'Rossi', 'elena@example.test', '+390212345678', 'I would like help finding a senior merchandising role.', 'Pending'],
            ['Noah', 'Williams', 'noah@example.test', '+442071234567', 'Can your team support an executive hospitality search?', 'Processed'],
        ] as [$firstName, $lastName, $email, $phone, $content, $status]) {
            $manager->persist(
                (new Contact())
                    ->setFirstName($firstName)
                    ->setLastName($lastName)
                    ->setEmail($email)
                    ->setPhoneNumber($phone)
                    ->setContent($content)
                    ->setStatus($contactStatuses[$status])
                    ->setCreatedAt(),
            );
        }

        $manager->flush();
    }
}
