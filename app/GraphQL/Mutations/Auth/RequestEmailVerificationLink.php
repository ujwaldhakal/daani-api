<?php

namespace App\GraphQL\Mutations\Auth;

use App\Events\Auth\RequestedEmailVerificationLink;
use App\Events\Auth\UserRegistered;
use App\GraphQL\Queries\User;
use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class RequestEmailVerificationLink
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue  Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args  The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context  Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo  Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {

        $user = \App\Repos\User::byEmail($args['email'])->first();

        if ($user && !$user->isVerified() && $user->canRequestForEmailVerificationLink()) {

            event(new RequestedEmailVerificationLink($user));
            $user->update(['requested_verification_link_count' => $user->getRequestedVerificationLinkCount()+1 ]);
            return $user;
        }

        return false;
    }
}
