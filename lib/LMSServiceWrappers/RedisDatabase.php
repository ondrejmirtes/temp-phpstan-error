<?php
/**
 * Separate databases to save on key size 
 * and better visibility of keys.
 * 
 * @author itay
 *
 */
abstract class LMSServiceWrappers_RedisDatabase
{

    public const 
                    SESSION         = 0, // saved for session keys and session related storage (like pager counts)
                    DATA            = 1, // Any data caching like orgs and content wrappers to containers links
                    SMARTGROUPS     = 2, // Smart Groups related keys.
                    LAUNCHER        = 3, // New Launcher related keys
                    AUTHTOKENS      = 4, // Auth tokens. Vouchers, App Keys, Signed links TODO move all here
                    EDUCATOR        = 5; // Any Course/Content persistant entries go here.
}
