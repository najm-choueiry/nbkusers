<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use Dompdf\Dompdf;
use Twig\Environment;

class Helper
{
    private $twig; // Add a private variable for Twig

    public function __construct(Environment $twig) // Inject Twig
    {
        $this->twig = $twig;
    }

    public static function processImages($images, $staticBaseDir, $folderName, $ImageFolder)
    {
        $processedImages = [];

        foreach ($images as $key => $imageData) {
            $image = $imageData['image'];
            $existingImagePath = $imageData['existingImagePath'];
            $imageName = $imageData['imageName'];

            if ($image) {
                if (!empty($existingImagePath)) {
                    $existingImagePathImage = explode('/', $existingImagePath)[3];
                    $oldImagePath = $staticBaseDir . $ImageFolder . '/' . $existingImagePathImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $imageType = explode('/', $image->getClientMimeType())[1];
                $imagePath = $staticBaseDir . 'imageUser/' . $folderName . '/' . $imageName . '.' . $imageType;
                $imagePathDB = 'imageUser/' . $folderName . '/' . $imageName . '.' . $imageType;
                $imageContent = file_get_contents($image->getPathname());
                if (file_put_contents($imagePath, $imageContent) === false) {
                    return new JsonResponse(['error' => 'Failed to save image content'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $processedImages[$key] = [
                    'path' => $imagePath,
                    'pathDB' => $imagePathDB,
                    'content' => $imageContent
                ];
            }
        }

        return $processedImages;
    }
    public function getBranchEmail($branchId)
	{
		$branchEmails = [1 => "sanayehbr@nbk.com.lb", 2 => "Bhamdounbr@nbk.com.lb", 3 => "PrivateBanking@nbk.com.lb"];
		// $branchEmails = [1 => "eliaschaaya97@gmail.com", 2 => "eliaschaaya97@gmail.com", 3 => "eliaschaaya97@gmail.com"];
		//$branchEmails = [1 => "zeina.abdallah@nbk.com.lb ", 2 => "maysaa.nasereddine@nbk.com.lb", 3 => "zeina.abdallah@nbk.com.lb "];
		if (array_key_exists($branchId, $branchEmails)) {
			return $branchEmails[$branchId];
		} else {
			return null;
		}
	}
    public function isValidEmail(string $email): bool
	{
		$pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
		return preg_match($pattern, $email) === 1;
	}
    public function unsetImagesFromData(&$data)
	{
		unset($data['frontImageID']);
		unset($data['backImageID']);
		unset($data['employerLetter']);
		unset($data['otherDocument']);
		unset($data['accountStatement']);
		unset($data['realEstateTitle']);
		return true;
	}
    public function generateReportPdf(array $data, $time = null, $userreference = null): string
    {
        if (!$time) {
            $time = new DateTime();
        }

        // utf8EncodeArray function
        $utf8EncodeArray = function ($input) use (&$utf8EncodeArray) {
            if (is_array($input)) {
				return array_map($utf8EncodeArray, $input);
			}
			if ($input instanceof DateTime) {
				return $input->format('Y-m-d H:i:s');
			}
			return $input !== null ? utf8_encode($input) : null;
        };

        $userreference = $utf8EncodeArray($userreference);
        $user = $utf8EncodeArray($data['user']);
        if (isset($data['user']['mothersName']))
        {
        $address = $utf8EncodeArray($data['address']);
        $workDetails = $utf8EncodeArray($data['workDetails']);
        $beneficiaryRightsOwner = $utf8EncodeArray($data['beneficiaryRightsOwner']);
        $politicalPositionDetails = $utf8EncodeArray($data['politicalPositionDetails']);
        $financialDetails = $utf8EncodeArray($data['financialDetails']);
        }
        if (isset($data['user']['mothersName']))
        {
        // Generate the HTML for the PDF using Twig
        $html = $this->twig->render('pdf/report.html.twig', [
            'reference' => $userreference,
            'user' => $user,
            'address' => $address,
            'workDetails' => $workDetails,
            'beneficiaryRightsOwner' => $beneficiaryRightsOwner,
            'politicalPositionDetails' => $politicalPositionDetails,
            'financialDetails' => $financialDetails,
            'time' => $time
        ]);
    }else{
        $html = $this->twig->render('pdf/existingrelationreport.html.twig', [
            'reference' => $userreference,
            'user' => $user,
            'time' => $time
        ]);
    }

        // PDF rendering logic
        $dompdf = new Dompdf();
        $dompdf->set_option('defaultFont', 'Helvetica');
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->loadHtml($html);
        $dompdf->render();
        return $dompdf->output();
    }

}
