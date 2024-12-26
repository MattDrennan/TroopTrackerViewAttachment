<?php

namespace TroopTrackerViewAttachment\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Attachment extends XFCP_Attachment
{
    public function actionIndex(ParameterBag $params)
    {
        /** @var Attachment $attachment */
        $attachment = $this->em()->find('XF:Attachment', $params->attachment_id);
        if (!$attachment)
        {
            throw $this->exception($this->notFound());
        }

        // If it's still a temp-hash (i.e. just uploaded, not associated),
        // enforce that hash check to prevent random access.
        if ($attachment->temp_hash)
        {
            $hash = $this->filter('hash', 'str');
            if ($attachment->temp_hash !== $hash)
            {
                return $this->noPermission();
            }
        }
        else
        {
            // >>> Enforce permissions for non-image files only <<<
            $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            // Extract the file extension
            $extension = strtolower(pathinfo($attachment->filename, PATHINFO_EXTENSION));

            // Enforce permissions if the file is NOT an image
            if (!in_array($extension, $allowedImageExtensions))
            {
                if (!$attachment->canView($error))
                {
                    return $this->noPermission($error);
                }
            }
        }

        // Enforce canonical URL for SEO purposes
        if (!$this->filter('no_canonical', 'bool'))
        {
            $this->assertCanonicalUrl($this->buildLink('attachments', $attachment));
        }

        // Use the core attachment plugin to display
        $attachPlugin = $this->plugin('XF:Attachment');
        return $attachPlugin->displayAttachment($attachment);
    }
}

?>