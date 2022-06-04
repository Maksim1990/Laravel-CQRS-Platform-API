<?php

use App\Exceptions\BehatRuntimeException;
use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Illuminate\Support\Facades\Storage;
use Imbo\BehatApiExtension\Context\ApiContext;

/**
 * Defines application features from the specific context.
 */
abstract class AbstractFeatureContext extends ApiContext implements Context
{
    protected const API_VERSION_PREFIX = '/api/v1';

    protected const ENV_FILE_DEFAULT = '.env';
    protected const ENV_FILE_BEHAT = '.env.testing';
    protected const ENV_FILE_TEMPORARY = '.env.temp';
    protected string $behatToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwM1wvZ3JhcGhxbCIsImlhdCI6MTYwMzYxNzYxNiwiZXhwIjoxNjAzNjIxMjE2LCJuYmYiOjE2MDM2MTc2MTYsImp0aSI6Ik5XYzNjeVVyWXBiNTc2ZGciLCJzdWIiOiI1ZjRlYTNlZDEyYzM3MDNlZmUxYjA1MzgiLCJwcnYiOiI4N2UwYWYxZWY5ZmQxNTgxMmZkZWM5NzE1M2ExNGUwYjA0NzU0NmFhIiwidXNlcl9pZCI6IjVmNGVhM2VkMTJjMzcwM2VmZTFiMDUzOCJ9.RJFP6lcHDMOsS9eSXoufSGe7sXPIfN58E_Xv5xQJQRk';

    protected function getResponseBodyContent()
    {
        $this->requireResponse();
        $response = json_decode($this->response->getBody()->getContents());
        if (isset($response->errors)) {
            throw new BehatRuntimeException($response->errors->message, $response->code);
        }

        return $response;
    }

    protected function getBehatToken(): string
    {
        return $this->behatToken;
    }

//    protected static function setupEnvironmentFromFileWithEnv($file, $type = 'env')
//    {
//        if (Storage::disk('root')->exists( $file)) {
//
//            if (Storage::disk('root')->exists(self::ENV_FILE_DEFAULT) && $type === 'env') {
//                self::deleteTemporaryFileIfExist(self::ENV_FILE_TEMPORARY);
//                Storage::disk('root')->copy(self::ENV_FILE_DEFAULT, self::ENV_FILE_TEMPORARY);
//            }
//
//            self::deleteTemporaryFileIfExist(self::ENV_FILE_DEFAULT);
//
//            Storage::disk('root')->copy( $file, self::ENV_FILE_DEFAULT);
//
//            if($type !== 'env'){
//                self::deleteTemporaryFileIfExist(self::ENV_FILE_TEMPORARY);
//            }
//        } else {
//            throw new PendingException($file . " does NOT exist");
//        }
//    }
//
//    protected static function deleteTemporaryFileIfExist($file)
//    {
//        if (Storage::disk('root')->exists($file)) {
//            Storage::disk('root')->delete($file);
//        }
//    }
}
