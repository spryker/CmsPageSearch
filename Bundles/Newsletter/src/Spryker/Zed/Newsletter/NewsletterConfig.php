<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Newsletter;

use Spryker\Shared\Newsletter\NewsletterConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class NewsletterConfig extends AbstractBundleConfig
{
    /**
     * @param string $token
     *
     * @return string
     */
    public function getDoubleOptInApproveTokenUrl($token)
    {
        return $this->getHostYves() . '/newsletter/approve?token=' . $token;
    }

    /**
     * @return string
     */
    public function getHostYves()
    {
        return $this->getConfig()->hasKey(NewsletterConstants::BASE_URL_YVES)
            ? $this->get(NewsletterConstants::BASE_URL_YVES)
            // @deprecated this is just for backward compatibility
            : $this->get(NewsletterConstants::HOST_YVES);
    }

    /**
     * @return string|null
     */
    public function getFromEmailName()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getFromEmailAddress()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getDoubleOptInConfirmationTemplateName()
    {
        return $this->get(NewsletterConstants::SHOP_MAIL_DOUBLE_OPT_IN_CONFIRMATION_TEMPLATE_NAME);
    }

    /**
     * @return string
     */
    public function getPasswordRestoreSubject()
    {
        return $this->get(NewsletterConstants::SHOP_MAIL_DOUBLE_OPT_IN_CONFIRMATION_SUBJECT);
    }

    /**
     * @return string
     */
    public function getMergeLanguage()
    {
        return $this->get(NewsletterConstants::MERGE_LANGUAGE_HANDLEBARS);
    }

    /**
     * @return array
     */
    public function getNewsletterTypes()
    {
        return [];
    }
}
