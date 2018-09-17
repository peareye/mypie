<?php
/**
 * Contact Controller
 */
namespace Piton\Controllers;

class ContactController extends BaseController
{
    /**
     * Message
     *
     * Accept POST contact message
     */
    public function submitMessage($request, $response, $args)
    {
        // Check honeypot for spammers
        if ($request->getParsedBodyParam('alt-email') !== 'alt@example.com') {
            // Just return and say nothing
            return $response->withRedirect($this->container->router->pathFor('thankYou'));
        }

        // Verify we have required fields
        if (!$request->getParsedBodyParam('fullname') ||
            !$request->getParsedBodyParam('email') ||
            !$request->getParsedBodyParam('message')) {
            // Return error
            // TODO go back to submitting page with validation error
            return $response->withRedirect($this->container->router->pathFor('thankYou'));
        }

        // Create contact message
        $contact = new \stdClass();
        $contact->name = filter_var($request->getParsedBodyParam('fullname'), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        $contact->email = filter_var($request->getParsedBodyParam('email'), FILTER_SANITIZE_EMAIL);
        $contact->subject = filter_var($request->getParsedBodyParam('subject'), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        $contact->message = filter_var($request->getParsedBodyParam('message'), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

        // Send email to admin
        $this->sendContactEmail($contact);

        // Return
        return $response->withRedirect($this->container->router->pathFor('thankYou'));
    }

    /**
     * Send Contact Email
     *
     * Sends contact email to site administrator
     * @param obj
     * @return void
     */
    protected function sendContactEmail(Object $contact)
    {
        // Get dependencies
        $email = $this->container->get('emailHandler');
        $log = $this->container->get('logger');
        $config = $this->container->get('settings');

        // Send message
        $email->setFrom($config['site']['sendFromEmail'], $config['site']['title'])
            ->setReplyTo($contact->email, $contact->name)
            ->setSubject($contact->subject)
            ->setMessage("From: {$contact->email}\n\n" . $contact->message);

        // Add as many To emails as in the config file
        if (is_array($config['site']['sendToEmail'])) {
            foreach ($config['site']['sendToEmail'] as $value) {
                $email->setTo($value, null);
            }
        } elseif (is_string($config['site']['sendToEmail'])) {
            $email->setTo($config['site']['sendToEmail'], null);
        }

        $email->send();

        $log->info('Contact message: ' . print_r($email, true));

        return;
    }

    /**
     * Thank You
     *
     * Display thank you page after POST submission
     */
    public function thankYou($request, $response, $args)
    {
        return $this->container->view->render($response, 'pages/_thankYou.html');
    }
}
