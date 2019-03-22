<?php

namespace MarketRedesign\MrdLogin;

use SocialiteProviders\Manager\SocialiteWasCalled;

class MrdLoginExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'mrdlogin', __NAMESPACE__.'\Provider'
        );
    }
}
