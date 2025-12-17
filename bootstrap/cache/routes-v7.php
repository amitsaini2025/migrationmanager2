<?php

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/oauth/token' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.token',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/authorize' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.authorizations.authorize',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'passport.authorizations.approve',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'passport.authorizations.deny',
          ),
          1 => NULL,
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/token/refresh' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.token.refresh',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/tokens' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.tokens.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/clients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.clients.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'passport.clients.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/scopes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.scopes.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/oauth/personal-access-tokens' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.personal.tokens.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'passport.personal.tokens.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/sanctum/csrf-cookie' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'sanctum.csrf-cookie',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/_ignition/health-check' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ignition.healthCheck',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/_ignition/execute-solution' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ignition.executeSolution',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/_ignition/update-config' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'ignition.updateConfig',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::KcSLUQvF7REOEAFC',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/admin-login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8zlZ3xblITPBDip3',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/refresh' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::gkZsTsSHp8a8mpLN',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/forgot-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::L0flMELwEVPj2krI',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/reset-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rMIdRUpBlHse5Qzu',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/countries' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::pUaHCFBZp7bzBoeV',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/visa-types' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::yE1oR3jFZW57OUL8',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/search-occupation' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::t64H9m5hUbnKwqv4',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::lClp2zseBsZltVEp',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/logout-all' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::gPAVZPvWXYOYEPtZ',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::cm3Agdt5G2vHh0yO',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Nj9CbBrkQc90XAph',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FSa8Zss5ES7p2j7t',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YEpFLkkciSACYISn',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/recent-cases' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::l1xJphkDvFEGxkZe',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vB2WE8RsNZug4oVS',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/upcoming-deadlines' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XS32lWEBO2TjJLQY',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/recent-activity' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Cu9O63NG6fCSHykE',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/matters' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jsp6tjpZaCd6CxxO',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/get-client-personal-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::CpV6slETIrLKvzQb',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-client-basic-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::pk8fOlvDXwLRjlDt',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-client-phone-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WFtl0kpvMOjsTC3Y',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-client-email-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EVZ5StmKhqp2RSQ2',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-client-address-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PoFdOXeYCVSymWgn',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-client-travel-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ME28gdlaua0yOJGo',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-client-qualification-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::cPcvwPB62WMZMUlT',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-client-experience-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DRasSzBRYN4Y8zJY',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-client-occupation-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::X0dKF2ITijWCEo9q',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-client-testscore-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Um8gOUqICBhwMKai',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-client-passport-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::2HlTOugzwk5W9FXC',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/delete-client-tab-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UOYK4F9K0i1DUH0P',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/delete-client-passport-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vmjTVIC0fppTm8R7',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/update-client-visa-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7TVz0tYWdgflPI9B',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents/personal/categories' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::yGdWiqwGuukqoK28',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents/personal/checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5IJXkbFSHVUwSghu',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents/visa/categories' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Arhm7tU0WbkeCEZQ',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents/visa/checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::MG9Gt1NOnjw6oTxI',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents/checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qgcmEfgQvjCSpdnG',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/documents/upload' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vHDJfti45dYmXREI',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/workflow/stages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Ikc0icWHbwANcBMe',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/workflow/allowed-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::sXByQr2UtoblTcj8',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/workflow/upload-allowed-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Be7dzFng1giazbtj',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/messages/send' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NfuA4hibpGGKvic5',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/messages/send-to-client' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::QAGzcDc49CS0KdPc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Y4VoCD0IlQaD25Sw',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/messages/unread-count' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nAu36EEDKqAKEYVe',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/payments/create-payment-intent' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::R1Qv07pDn8Yop8U4',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/notifications/broadcasts/unread' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LBR4HhD2Fh8UnIE1',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/notifications/broadcasts' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::n6aQtvKnOHCpQcYL',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Q89fEfv2CIeQ7ezC',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/broadcasting/auth' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xE0jhhWlV5F8o2tU',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/service-account/generate-token' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::etQqYtawqbDH778h',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::E5BoGm97jCBIDX69',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clear-cache' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Cw2139nlvwpMHTXo',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/exception' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'exception.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'exception.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matter.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matter.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matter.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/tags' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.tags.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/tags/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.tags.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/tags/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.tags.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/email-labels' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emaillabels.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/email-labels/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emaillabels.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/email-labels/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emaillabels.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/workflow' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/workflow/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/workflow/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/emails' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emails.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/emails/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emails.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/emails/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emails.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/crm-email-template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.crmemailtemplate.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/crm-email-template/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.crmemailtemplate.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/crm-email-template/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.crmemailtemplate.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter-email-template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matteremailtemplate.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter-email-template/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matteremailtemplate.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter-email-template/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matteremailtemplate.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter-other-email-template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matterotheremailtemplate.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter-other-email-template/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matterotheremailtemplate.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/matter-other-email-template/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matterotheremailtemplate.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/personal-document-type' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.personaldocumenttype.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/personal-document-type/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.personaldocumenttype.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/personal-document-type/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.personaldocumenttype.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/visa-document-type' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.visadocumenttype.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/visa-document-type/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.visadocumenttype.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/visa-document-type/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.visadocumenttype.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/document-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.documentchecklist.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/document-checklist/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.documentchecklist.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/document-checklist/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.documentchecklist.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/history' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.history',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/statistics' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.statistics',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/send' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.send.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.send',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/send/template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.send.template',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/send/bulk' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.send.bulk',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/templates/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/sms/templates-active' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.active',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/esignature' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.esignature.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/features/esignature/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.esignature.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/users/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/users/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/roles' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.roles.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/roles/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.roles.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/roles/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.roles.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/teams' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.teams.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/teams/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.teams.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/offices' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/offices/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/offices/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/system/clientsemaillist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.clientsemaillist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/database/anzsco' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/database/anzsco/data' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.data',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/database/anzsco/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/adminconsole/database/anzsco/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'crm.login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'crm.login.post',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'crm.logout',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'crm.logout.get',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/column-preferences' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.column-preferences',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/update-stage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.update-stage',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/extend-deadline' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.extend-deadline',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/update-task-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.update-task-completed',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/fetch-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.fetch-notifications',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/fetch-office-visit-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.fetch-office-visit-notifications',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/mark-notification-seen' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.mark-notification-seen',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/fetch-visa-expiry-messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.fetch-visa-expiry-messages',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/fetch-in-person-waiting-count' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.fetch-in-person-waiting-count',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/fetch-total-activity-count' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.fetch-total-activity-count',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/check-checkin-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.check-checkin-status',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/update-checkin-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.update-checkin-status',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/my_profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'my_profile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'my_profile.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/change_password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'change_password',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'change_password.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::SzUno8Gi6Ld4qI3R',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/approved_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::2RnEyqKNKTi12c5R',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/process_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::pA3ydW0Qg2j7NJmQ',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/archive_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xrhxQ9x0VwulBgR6',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/declined_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ovsd7ATm7kZePXxF',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/delete_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::3LEssW439PwEmiuV',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/move_action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9wrWuNagIP1oNRHG',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/appointments-education' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments-education',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/appointments-jrp' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments-jrp',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/appointments-tourist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments-tourist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/appointments-others' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments-others',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/add_ckeditior_image' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'add_ckeditior_image',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get_chapters' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'get_chapters',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get_states' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::hjZ6BC24RRV7TmrT',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/settings/taxes/returnsetting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'returnsetting',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/settings/taxes/savereturnsetting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'savereturnsetting',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/getsubcategories' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::LZa8rp5PrmcQuRez',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/getassigneeajax' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::51gjSn3Ckem847oQ',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/getpartnerajax' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PbvmMHugc10wGjR6',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/checkclientexist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::C3ngxuj7fgCxKmwM',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/notifications/broadcasts/manage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'notifications.broadcasts.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard/active-users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard.active-users',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/notifications/broadcasts/send' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'notifications.broadcasts.send',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/notifications/broadcasts/history' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'notifications.broadcasts.history',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/notifications/broadcasts/my-history' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'notifications.broadcasts.my-history',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/notifications/broadcasts/read-history' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'notifications.broadcasts.read-history',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/notifications/broadcasts/unread' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'notifications.broadcasts.unread',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/user-login-analytics' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'user-login-analytics.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/user-login-analytics/daily' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'api.user-login-analytics.daily',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/user-login-analytics/weekly' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'api.user-login-analytics.weekly',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/user-login-analytics/monthly' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'api.user-login-analytics.monthly',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/user-login-analytics/hourly' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'api.user-login-analytics.hourly',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/user-login-analytics/summary' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'api.user-login-analytics.summary',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/user-login-analytics/top-users' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'api.user-login-analytics.top-users',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/user-login-analytics/trends' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'api.user-login-analytics.trends',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/leads' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/leads/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/leads/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/leads/followups/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/leads/analytics/trends' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.analytics.trends',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/leads/analytics/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.analytics.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/leads/analytics/compare-agents' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.analytics.compare',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-notedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'get-notedetail',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/email_templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'email.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/email_templates/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'email.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/email_templates/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'email.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/edit_email_template' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'edit_email_template.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api-key' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'api',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'api.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clientsmatterslist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.clientsmatterslist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clientsemaillist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.clientsemaillist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/edit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/save-section' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveSection',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/phone/send-otp' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.phone.sendOTP',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/phone/verify-otp' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.phone.verifyOTP',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/phone/resend-otp' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.phone.resendOTP',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/email/send-verification' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.email.sendVerification',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/email/resend-verification' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.email.resendVerification',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/followup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EAlTwODOBbUTA42C',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/followup/retagfollowup' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7KgtmXSHnWvp0NQm',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/removetag' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ZwASEfzrfflnqMLX',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/get-recipients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getrecipients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/get-onlyclientrecipients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getonlyclientrecipients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/get-allclients' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getallclients',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/change_assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::SPDN6slJahUwYmLf',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.gettemplates',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/sendmail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.sendmail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/upload-mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::MONDBQ6lxQQf2jpr',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/upload-fetch-mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'email.upload.inbox',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/upload-sent-fetch-mail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'email.upload.sent',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/email/check-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'email.check.service',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/reassiginboxemail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.reassiginboxemail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/reassigsentemail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.reassigsentemail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/listAllMattersWRTSelClient' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.listAllMattersWRTSelClient',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/updatemailreadbit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updatemailreadbit',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/filter-emails' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.filter.emails',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/filter-sentemails' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.filter.sentmails',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/mail/enhance' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'mail.enhance',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/email-labels' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'email-labels.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'email-labels.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/email-labels/apply' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'email-labels.apply',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/email-labels/remove' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'email-labels.remove',
          ),
          1 => NULL,
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/create-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.createnote',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update-note-datetime' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updateNoteDatetime',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/getnotedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getnotedetail',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/deletenote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.deletenote',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/viewnotedetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::qOqMKTUfZwss5rJX',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/viewapplicationnote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::x3pJ6S3djQ5IQFHP',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/saveprevvisa' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nU8CRpx0aBwrslFD',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/saveonlineprimaryform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::upsF7grQrBStX3IT',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/saveonlinesecform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EP52I2nJuGD4Z5ow',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/saveonlinechildform' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4wQRXmUjeZIrNKOW',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-notes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getnotes',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/pinnote' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xqjy16jHRfj6fX96',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/convert-activity-to-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.convertActivityToNote',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/archived' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.archived',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/change-client-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updateclientstatus',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-activities' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.activities',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/deletecostagreement' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.deletecostagreement',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/deleteactivitylog' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.deleteactivitylog',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/not-picked-call' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.notpickedcall',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/pinactivitylog' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wMHdBhfRaH1soHaV',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/interested-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::tbknAIiU6iixViIg',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/edit-interested-service' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::N2OsGCZsiymD4syl',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-services' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Bm9V6Pd3cM8XV73R',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/servicesavefee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::BSIheMbRYEwL0jWH',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/getintrestedservice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Qf6vcnHPPuwIvQRi',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/getintrestedserviceedit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9s1n9mSEbD0O2Xd7',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-application-lists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getapplicationlists',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/saveapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveapplication',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/convertapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.convertapplication',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/deleteservices' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.deleteservices',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/savetoapplication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8mnZXUfWHCPBU7hv',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/client/createservicetaken' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Bw15jhUzoV8mnvbt',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/client/removeservicetaken' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::VIO3IfQTLkneNJ9o',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/client/getservicetaken' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Dzmfl1XbyKEJ8WMa',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/add-edu-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.addedudocchecklist',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/upload-edu-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.uploadedudocument',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/add-visa-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.addvisadocchecklist',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/upload-visa-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.uploadvisadocument',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/rename' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.renamedoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/delete' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.deletedocs',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/get-visa-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.getvisachecklist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/not-used' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.notuseddoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/rename-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.renamechecklistdoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/delete-checklist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.deleteChecklist',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/back-to-doc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.backtodoc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/download' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.download',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/add-personal-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.addPersonalDocCategory',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/update-personal-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.updatePersonalDocCategory',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/add-visa-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.addVisaDocCategory',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/update-visa-category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.documents.updateVisaDocCategory',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/saveaccountreport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveaccountreport.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/test-python-accounting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.test-python-accounting',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/saveinvoicereport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveinvoicereport.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/saveadjustinvoicereport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveadjustinvoicereport.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/saveofficereport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveofficereport.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/savejournalreport' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.savejournalreport.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/isAnyInvoiceNoExistInDB' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.isAnyInvoiceNoExistInDB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/listOfInvoice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.listOfInvoice',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/getTopReceiptValInDB' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getTopReceiptValInDB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/getInfoByReceiptId' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getInfoByReceiptId',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/getTopInvoiceNoFromDB' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getTopInvoiceNoFromDB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/clientLedgerBalanceAmount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.clientLedgerBalanceAmount',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/analytics-dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.analytics-dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/insights' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.insights',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/invoicelist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.invoicelist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/void_invoice' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'client.void_invoice',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/clientreceiptlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.clientreceiptlist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/officereceiptlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.officereceiptlist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/journalreceiptlist' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.journalreceiptlist',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/validate_receipt' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'client.validate_receipt',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/delete_receipt' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::2IQ88phT5LCfxHZB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update-client-funds-ledger' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.update-client-funds-ledger',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update-office-receipt' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updateOfficeReceipt',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-invoices-by-matter' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getInvoicesByMatter',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update-client-fund-ledger' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updateClientFundLedger',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/invoiceamount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.invoiceamount',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/upload-clientreceipt-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.uploadclientreceiptdocument',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/upload-officereceipt-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.uploadofficereceiptdocument',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/upload-journalreceipt-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.uploadjournalreceiptdocument',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/update-address' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updateAddress',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/search-address-full' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.searchAddressFull',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/get-place-details' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getPlaceDetails',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/address_auto_populate' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::KY7t8BQB08a17Bb3',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/fetchClientContactNo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::S5zEdfEzxgaMwFCy',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/clientdetailsinfo' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.clientdetailsinfo.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-visa-types' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'getVisaTypes',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-countries' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'getCountries',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/updateOccupation' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updateOccupation',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/search-partner' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.searchPartner',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/search-partner-test' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.searchPartnerTest',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/test-bidirectional' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.testBidirectional',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/save-relationship' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveRelationship',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/generateagreement' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.generateagreement',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/getMigrationAgentDetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getMigrationAgentDetail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/getVisaAggreementMigrationAgentDetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getVisaAggreementMigrationAgentDetail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/getCostAssignmentMigrationAgentDetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getCostAssignmentMigrationAgentDetail',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/savecostassignment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.savecostassignment',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/check-cost-assignment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::XipiKItHWxoQnisM',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/savecostassignmentlead' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.savecostassignmentlead',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/getCostAssignmentMigrationAgentDetailLead' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getCostAssignmentMigrationAgentDetailLead',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/forms' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'forms.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-matter-templates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getmattertemplates',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/fetchClientMatterAssignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::I1kNZGqlLzigGGuC',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/updateClientMatterAssignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WF1WGyM3AJLxV2FB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/upload-checklists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'upload_checklists.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/upload-checklists/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'upload_checklistsupload',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/personalfollowup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::hdGVh4UbQCxn3MgW',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/updatefollowup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OrLcfm01aPA1cvoU',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/reassignfollowup/store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::faaYFCZFEDQN7u5z',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/update-session-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.updatesessioncompleted',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/getAllUser' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getAllUser',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/toggle-client-portal' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.toggleClientPortal',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/client-portal-details/approve-audit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.approveAuditValue',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/api/client-portal-details/reject-audit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.rejectAuditValue',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/anzsco/search' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'anzsco.search',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/check-email' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'check.email',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/check.phone' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'check.phone',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/save_tag' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xseuxiBLDChhaJLX',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/save-references' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'references.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/check-star-client' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'check.star.client',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/merge_records' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'client.merge_records',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/send-webhook' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'send-webhook',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fetch-visa_expiry_messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::6TVRMKEfOVS0WD8w',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/applications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'applications.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/applications/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'applications.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/applications-import' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'applications.import',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/getapplicationdetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GdJ5MbFWau7nzNAS',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/load-application-insert-update-data' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::lU2WXdJpOzCZKykK',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/updatestage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ffd8Aiau1EZ7kbEl',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/completestage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::marmVqs8dVA0He56',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/updatebackstage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::yDc6I2WUXjSjRNgD',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-applications-logs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::jCvCFKUh7Az7VukU',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-applications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FAbqAB1rm0UnBWPW',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/discontinue_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::eA4VHvzNSWTMivdV',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/revert_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rRw8WLGCxhuoNLsj',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/create-app-note' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::hJwulnCcrxgrT93P',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/getapplicationnotes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::oPndjGgHgkCgrORa',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application-sendmail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4EUk4IO2wtfl24m3',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/matter-messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.matter-messages',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/clients/send-message' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.send-message',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application/updateintake' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PSqflSYHpYhr6CWj',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application/updatedates' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::x20b08M78zdp8B8X',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application/updateexpectwin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PLfNqFPBdPcmruKP',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application/getapplicationbycid' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Pn8ZzwBpBcXswqAJ',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application/spagent_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::NJJc0si4zSriE2ag',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application/sbagent_application' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::YqQdxDakSAd5dGdr',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application/application_ownership' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::j5OQv8TnvH3wn3wZ',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/superagent' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::pId8LXcx8PsvoTqq',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/subagent' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DiaOlBy798g0x5vW',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/applicationsavefee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::SZJI3RjJt8MR59bO',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/add-checklists' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Vmm5fqMzic4dFBRc',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application/checklistupload' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::SNXkVzlvGR4caO1T',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/deleteapplicationdocs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DL0m4y4JEideNlAU',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application/publishdoc' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wHCI2qUQq6AhC7EL',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application/approve-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::rmoiTydaVAj3F9MH',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application/reject-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::yr4sN9phNckyrlrA',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/application/download-document' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::selsTB7zd78D2VgD',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/migration' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'migration.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/office-visits' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'officevisits.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/office-visits/waiting' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'officevisits.waiting',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/office-visits/attending' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'officevisits.attending',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/office-visits/completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'officevisits.completed',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/office-visits/archived' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'officevisits.archived',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/office-visits/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'officevisits.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/checkin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EacnK5NdWZE0OY6U',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-checkin-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4CKMnrqiDe0hkKIc',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update_visit_purpose' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::OBUrzvk26sopQNvz',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update_visit_comment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::M703Sivt9BaDmrCl',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/attend_session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kkHat1ifBW5ReJN0',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/complete_session' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::3YJ5pyxvEBm85Axd',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/office-visits/change_assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4PCyFUSHLrrl9vP4',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/appointments' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/appointments/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/appointments-adelaide' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments-adelaide',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/deleteappointment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::bzakwN6RvzG4o72j',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/add-appointment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WS1lAN3WxJysGXyd',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/add-appointment-book' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8VsoW497vTKBxwxi',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/editappointment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Gc1twDezEwlKDUMr',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/updatefollowupschedule' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::iPfdD29xQYiGyXiC',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update_appointment_status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::v2xazoVNTj3mhBmX',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update_appointment_priority' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xIhP1q1kEZrkUG8V',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update_apppointment_comment' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::8LObr7s8skms99M8',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update_apppointment_description' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::xJD89dftCdDeelTB',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-appointments' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::cbOUff9IZarM0TSt',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/getAppointmentdetail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::DwytQf8PDrQMboCy',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get-assigne-detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ou9pHVvMS1yae5lB',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/change_assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::HJTb87W0DdcWfrS5',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/getdatetimebackend' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'getdatetimebackend',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/getdisableddatetime' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'getdisableddatetime',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/booking/appointments' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/booking/appointments/bulk-update-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.bulk-update-status',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/booking/appointments/export' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.export',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/booking/sync/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.sync.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/booking/sync/stats' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.sync.stats',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/booking/sync/manual' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.sync.manual',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/booking/api/appointments' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.api.appointments',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/audit-logs' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'auditlogs.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fetch-notification' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::Yq4KEjAoyqKOpTWE',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fetch-messages' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::fWGMz7kaZn1Q4sBL',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fetch-office-visit-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::6TjzMn7Nzbx4vgmG',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/mark-notification-seen' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::HRS1yBvgsr0rKsot',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/check-checkin-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::otZiQqc5v97TgvjD',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update-checkin-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::91bfcUOnUTMVXR0C',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/all-notifications' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EeM5f9bLqthcIQg3',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fetch-InPersonWaitingCount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::U58PkzaWCsdPzevT',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/fetch-TotalActivityCount' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::44gjLdW68eFPMZGV',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/assignee' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/assignee/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/assignee-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::5zMqKJ3UQLmKnSqY',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update-task-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::UTJlwDG240aw566B',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update-task-not-completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wqkUU1XqbV6ZRwX4',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/assigned_by_me' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.assigned_by_me',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/assigned_to_me' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.assigned_to_me',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/action_completed' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.action_completed',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/is_email_unique' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::9dYNvIE828M7r2WH',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/is_contactno_unique' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::EouqD9PsPZDROHzG',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/extenddeadlinedate' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::pxRenikyCePmmq7z',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update-stage' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::tASMWHOieFpmXZJh',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/get_assignee_list' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::nbio4ftiz5TdQS3h',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/update-task' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::PKdxSCzuhm0APXvk',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/action/counts' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'action.counts',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/action' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.action',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/action/list' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'action.list',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/test-signature' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'test.signature',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/doc-to-pdf' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'doc-to-pdf.form',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/doc-to-pdf/convert' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'doc-to-pdf.convert',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/doc-to-pdf/test' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'doc-to-pdf.test',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/doc-to-pdf/test-python' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'doc-to-pdf.test-python',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/doc-to-pdf/debug' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'doc-to-pdf.debug',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/signatures' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/signatures/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/signatures/suggest-association' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.suggest-association',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/signatures/preview-email' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.preview-email',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/signatures/bulk-archive' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.bulk-archive',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/signatures/bulk-void' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.bulk-void',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/signatures/bulk-resend' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.bulk-resend',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/documents/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/webhooks/sms/twilio/status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'webhooks.sms.twilio.status',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/webhooks/sms/twilio/incoming' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'webhooks.sms.twilio.incoming',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/webhooks/sms/cellcast/status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'webhooks.sms.cellcast.status',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/webhooks/sms/cellcast/incoming' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'webhooks.sms.cellcast.incoming',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/test-messaging' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7GVklrR9h8QuUo6X',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/test-broadcast' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::WL9gfFozjy4koSfW',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/test-create-message' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::c0AEwAEXB6d3WFP5',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/broadcasting/auth' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::VqVSL1L5EzncAKUn',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'POST' => 1,
            'HEAD' => 2,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/oauth/(?|tokens/([^/]++)(*:32)|clients/([^/]++)(?|(*:58))|personal\\-access\\-tokens/([^/]++)(*:99))|/c(?|aptcha(?|/api(?:/([^/]++))?(*:139)|(?:/([^/]++))?(*:161))|lients/(?|e(?|dit/([^/]++)(*:196)|mail/status/([^/]++)(*:224))|p(?|artner\\-eoi\\-data/([^/]++)(*:263)|hone/status/([^/]++)(*:291))|changetype/([^/]++)/([^/]++)(*:328)|detail/([^/]++)(?:/([^/]++)(?:/([^/]++))?)?(*:379)|([^/]++)/eoi\\-roi(?|(*:407)|/(?|calculate\\-points(*:436)|([^/]++)(?|(*:455)|/reveal\\-password(*:480)))|(*:490))|s(?|ave(?|a(?|ccountreport/([^/]++)(*:534)|djustinvoicereport/([^/]++)(*:569))|invoicereport/([^/]++)(*:600)|officereport/([^/]++)(*:629)|journalreport/([^/]++)(*:659))|endToHubdoc/([^/]++)(*:688))|gen(?|Invoice/([^/]++)(*:719)|ClientFundReceipt/([^/]++)(*:753)|OfficeReceipt/([^/]++)(*:783))|c(?|heckHubdocStatus/([^/]++)(*:821)|lientdetailsinfo/([^/]++)(*:854))|printPreview/([^/]++)(*:884)|([^/]++)/(?|upload\\-agreement(*:921)|matters(*:936))))|/a(?|p(?|i/(?|workflow/stages/([^/]++)(*:985)|messages/([^/]++)(?|/read(*:1018)|(*:1027))|notifications/broadcasts/([^/]++)(?|(*:1073)|/read(*:1087)))|p(?|lication(?|s/detail/([^/]++)(*:1130)|/export/pdf/([^/]++)(*:1159))|ointments/([^/]++)(?|(*:1190)|/edit(*:1204)|(*:1213))))|dminconsole/(?|features/(?|matter(?|/(?|edit/([^/]++)(*:1278)|([^/]++)(*:1295))|\\-(?|email\\-template/(?|edit/([^/]++)(*:1342)|([^/]++)(*:1359))|other\\-email\\-template/(?|edit/([^/]++)(*:1408)|([^/]++)(*:1425))))|tags/(?|edit/([^/]++)(*:1458)|([^/]++)(*:1475))|email(?|\\-labels/(?|edit/([^/]++)(*:1518)|([^/]++)(*:1535))|s/(?|edit/([^/]++)(*:1563)|([^/]++)(*:1580)))|workflow/(?|edit/([^/]++)(*:1616)|([^/]++)(*:1633)|deactivate\\-workflow/([^/]++)(*:1671)|activate\\-workflow/([^/]++)(*:1707))|crm\\-email\\-template/(?|edit/([^/]++)(*:1754)|([^/]++)(*:1771))|personal\\-document\\-type/(?|edit/([^/]++)(*:1822)|([^/]++)(*:1839)|checkcreatefolder(*:1865))|visa\\-document\\-type/(?|edit/([^/]++)(*:1912)|([^/]++)(*:1929)|checkcreatefolder(*:1955))|document\\-checklist/(?|edit/([^/]++)(*:2001)|([^/]++)(*:2018))|sms/(?|history/([^/]++)(*:2051)|status/([^/]++)(*:2075)|templates/([^/]++)(?|(*:2105)|/edit(*:2119)|(*:2128))))|system/(?|users/(?|edit/([^/]++)(*:2172)|view/([^/]++)(*:2194)|([^/]++)(*:2211)|s(?|avezone(*:2231)|toreclient(*:2250))|active(*:2266)|in(?|active(*:2286)|vited(*:2300))|c(?|lient(?|list(*:2326)|/([^/]++)(*:2344))|reateclient(*:2365))|editclient/([^/]++)(*:2394))|roles/(?|edit/([^/]++)(*:2426)|([^/]++)(*:2443))|teams/(?|edit/([^/]++)(*:2475)|([^/]++)(*:2492))|offices/(?|edit/([^/]++)(*:2526)|view/(?|([^/]++)(*:2551)|client/([^/]++)(*:2575))|([^/]++)(*:2593)))|database/anzsco/(?|edit/([^/]++)(*:2636)|([^/]++)(*:2653)|import(?|(*:2671))))|nzsco/code/([^/]++)(*:2702)|ssignee/([^/]++)(?|(*:2730)|/edit(*:2744)|(*:2753)))|/notifications/broadcasts/(?|([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/details(*:2863)|([0-9]+)/read(*:2885)|([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})(*:2956))|/leads/(?|detail/([^/]++)(*:2991)|history/([^/]++)(*:3016)|([^/]++)(?|/edit(*:3041)|(*:3050))|assign(?|(*:3069)|able\\-users(*:3089))|bulk\\-(?|assign(*:3114)|convert(*:3130))|conver(?|t(?|(*:3153)|\\-single(*:3170))|sion\\-stats(*:3191))|followups(?|(*:3213)|/([^/]++)/(?|c(?|omplete(*:3246)|ancel(*:3260))|reschedule(*:3280)))|([^/]++)/followups(*:3309)|analytics(*:3327)|notes/delete/([^/]++)(*:3357)|pin/([^/]++)(*:3378)|updateOccupation(*:3403))|/edit_email_template/([^/]++)(*:3442)|/d(?|ocument(?|/download/pdf/([^/]++)(*:3488)|s(?|/([^/]++)/(?|sign(*:3518)|page/([^/]++)(*:3540))|(?:/([0-9]+))?(*:3564)|/(?|([^/]++)/download\\-signed(?|(*:3605)|\\-and\\-thankyou(*:3629))|thankyou(?:/([^/]++))?(*:3661)|([^/]++)(?|/(?|s(?|end\\-(?|reminder(*:3705)|signing\\-link(*:3727))|ign(*:3740))|edit(*:3754))|(*:3764)))|(*:3775)))|e(?|stroy_(?|by_me/([^/]++)(*:3813)|to_me/([^/]++)(*:3836)|activity/([^/]++)(*:3862)|complete_activity/([^/]++)(*:3897))|bug\\-pdf\\-page/([^/]++)/([^/]++)(*:3939)))|/mail\\-attachments/(?|([^/]++)/(?|download(*:3992)|preview(*:4008))|email/([^/]++)/download\\-all(*:4046))|/forms/([^/]++)(?|(*:4074)|/p(?|review(*:4094)|df(*:4105)))|/get\\-client\\-matters/([^/]++)(*:4146)|/up(?|load\\-checklists/matter/([^/]++)(*:4193)|dateappointmentstatus/([^/]++)/([^/]++)(*:4241))|/verify\\-email/([^/]++)(*:4274)|/booking/(?|appointments/(?|([0-9]+)/edit(*:4324)|([0-9]+)(?|(*:4344))|([0-9]+)/json(*:4367)|([0-9]+)/update\\-status(*:4399)|([0-9]+)/update\\-consultant(*:4435)|([0-9]+)/update\\-datetime(*:4469)|([0-9]+)/add\\-note(*:4496)|([0-9]+)/update\\-followup(*:4530)|([0-9]+)/send\\-reminder(*:4562))|calendar/(paid|jrp|education|tourist|adelaide)(*:4618))|/sign(?|atures/(?|([^/]++)(?|(*:4657)|/(?|reminder(*:4678)|send(*:4691)|copy\\-link(*:4710)|associate(*:4728)))|api/client\\-matters/([^/]++)(*:4767)|([^/]++)/detach(*:4791))|/([^/]++)/([^/]++)(*:4819))|/test\\-(?|delete\\-message/([^/]++)(*:4863)|mark\\-read/([^/]++)(*:4891)))/?$}sDu',
    ),
    3 => 
    array (
      32 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.tokens.destroy',
          ),
          1 => 
          array (
            0 => 'token_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      58 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.clients.update',
          ),
          1 => 
          array (
            0 => 'client_id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'passport.clients.destroy',
          ),
          1 => 
          array (
            0 => 'client_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      99 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'passport.personal.tokens.destroy',
          ),
          1 => 
          array (
            0 => 'token_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      139 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::X5uJobyCltR9CX4y',
            'config' => NULL,
          ),
          1 => 
          array (
            0 => 'config',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      161 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::IgCPOVg8Yp9lTBlS',
            'config' => NULL,
          ),
          1 => 
          array (
            0 => 'config',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      196 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      224 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.email.status',
          ),
          1 => 
          array (
            0 => 'emailId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      263 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.partnerEoiData',
          ),
          1 => 
          array (
            0 => 'partnerId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      291 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.phone.status',
          ),
          1 => 
          array (
            0 => 'contactId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      328 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::2MawSvtbgyA6MDzZ',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'type',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      379 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.detail',
            'client_unique_matter_ref_no' => NULL,
            'tab' => NULL,
          ),
          1 => 
          array (
            0 => 'client_id',
            1 => 'client_unique_matter_ref_no',
            2 => 'tab',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      407 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.eoi-roi.index',
          ),
          1 => 
          array (
            0 => 'client',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      436 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.eoi-roi.calculatePoints',
          ),
          1 => 
          array (
            0 => 'client',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      455 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.eoi-roi.show',
          ),
          1 => 
          array (
            0 => 'client',
            1 => 'eoiReference',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'clients.eoi-roi.destroy',
          ),
          1 => 
          array (
            0 => 'client',
            1 => 'eoiReference',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      480 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.eoi-roi.revealPassword',
          ),
          1 => 
          array (
            0 => 'client',
            1 => 'eoiReference',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      490 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.eoi-roi.upsert',
          ),
          1 => 
          array (
            0 => 'client',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      534 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveaccountreport',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      569 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveadjustinvoicereport',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      600 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveinvoicereport',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      629 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.saveofficereport',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      659 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.savejournalreport',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      688 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.sendToHubdoc',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      719 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::ARmwSaeMSy65Z26x',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      753 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4GrJApF4iqmAEVsN',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      783 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::wUw75fgSHQEFOYIc',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      821 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.checkHubdocStatus',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      854 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.clientdetailsinfo',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      884 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::fYS4GLAgTYfsXegH',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      921 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.uploadAgreement',
          ),
          1 => 
          array (
            0 => 'admin',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      936 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.matters',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      985 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::c26i61V5G3Jwjl3x',
          ),
          1 => 
          array (
            0 => 'stage_id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1018 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::0WirUH1Ek4xPcAtA',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1027 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::kX0oBm3ovEJBTxs2',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1073 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::vWWYnj51hWanJ6Z4',
          ),
          1 => 
          array (
            0 => 'batchUuid',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1087 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::4qnSBlogC0wexf3y',
          ),
          1 => 
          array (
            0 => 'notificationId',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1130 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'applications.detail',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1159 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::dzkb4qcL2d9kqOG8',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1190 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.show',
          ),
          1 => 
          array (
            0 => 'appointment',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1204 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.edit',
          ),
          1 => 
          array (
            0 => 'appointment',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1213 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.update',
          ),
          1 => 
          array (
            0 => 'appointment',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'appointments.destroy',
          ),
          1 => 
          array (
            0 => 'appointment',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1278 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matter.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1295 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matter.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1342 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matteremailtemplate.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1359 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matteremailtemplate.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1408 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matterotheremailtemplate.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1425 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.matterotheremailtemplate.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1458 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.tags.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1475 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.tags.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1518 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emaillabels.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1535 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emaillabels.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1563 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emails.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1580 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.emails.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1616 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1633 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1671 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.deactivate',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1707 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.workflow.activate',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1754 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.crmemailtemplate.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1771 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.crmemailtemplate.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1822 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.personaldocumenttype.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1839 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.personaldocumenttype.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1865 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1912 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.visadocumenttype.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1929 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.visadocumenttype.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1955 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.generated::4y1nsuhm2M8AQM2i',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2001 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.documentchecklist.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2018 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.documentchecklist.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2051 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.history.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2075 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.status.check',
          ),
          1 => 
          array (
            0 => 'smsLogId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2105 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.show',
          ),
          1 => 
          array (
            0 => 'template',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2119 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.edit',
          ),
          1 => 
          array (
            0 => 'template',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2128 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.update',
          ),
          1 => 
          array (
            0 => 'template',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.features.sms.templates.destroy',
          ),
          1 => 
          array (
            0 => 'template',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2172 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2194 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.view',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2211 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2231 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2250 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.storeclient',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2266 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.active',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2286 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.inactive',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2300 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.invited',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2326 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.clientlist',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2344 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.updateclient',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2365 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.createclient',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2394 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.users.editclient',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2426 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.roles.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2443 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.roles.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2475 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.teams.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2492 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.teams.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2526 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2551 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.view',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2575 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.viewclient',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2593 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.system.offices.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2636 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2653 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2671 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.import',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'adminconsole.database.anzsco.import.store',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2702 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'anzsco.getByCode',
          ),
          1 => 
          array (
            0 => 'code',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2730 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.show',
          ),
          1 => 
          array (
            0 => 'assignee',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2744 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.edit',
          ),
          1 => 
          array (
            0 => 'assignee',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2753 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.update',
          ),
          1 => 
          array (
            0 => 'assignee',
          ),
          2 => 
          array (
            'PUT' => 0,
            'PATCH' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy',
          ),
          1 => 
          array (
            0 => 'assignee',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2863 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'notifications.broadcasts.details',
          ),
          1 => 
          array (
            0 => 'batchUuid',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2885 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'notifications.broadcasts.read',
          ),
          1 => 
          array (
            0 => 'notificationId',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      2956 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'notifications.broadcasts.delete',
          ),
          1 => 
          array (
            0 => 'batchUuid',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      2991 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.detail',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3016 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.history',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3041 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3050 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'leads.patch',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PATCH' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3069 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.assign',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3089 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.assignable_users',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3114 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.bulk_assign',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3130 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.bulk_convert',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3153 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.convert',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3170 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.convert_single',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3191 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.conversion_stats',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3213 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.index',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.store',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3246 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.complete',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3260 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.cancel',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3280 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.reschedule',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3309 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.followups.get',
          ),
          1 => 
          array (
            0 => 'leadId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3327 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.analytics.index',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3357 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.notes.delete',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3378 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.pin',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3403 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'leads.updateOccupation',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3442 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'edit_email_template',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3488 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::TeRHpxCMo295bRvl',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3518 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'public.documents.submitSignatures',
          ),
          1 => 
          array (
            0 => 'document',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3540 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'public.documents.page',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'page',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3564 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.index',
            'id' => NULL,
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3605 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.download.signed',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3629 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.download_and_thankyou',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3661 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'public.documents.thankyou',
            'id' => NULL,
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3705 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.sendReminder',
          ),
          1 => 
          array (
            0 => 'document',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3727 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.sendSigningLink',
          ),
          1 => 
          array (
            0 => 'document',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3740 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.showSignForm',
          ),
          1 => 
          array (
            0 => 'document',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3754 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3764 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PATCH' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3775 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'documents.store',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      3813 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy_by_me',
          ),
          1 => 
          array (
            0 => 'note_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3836 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy_to_me',
          ),
          1 => 
          array (
            0 => 'note_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3862 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy_activity',
          ),
          1 => 
          array (
            0 => 'note_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3897 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'assignee.destroy_complete_activity',
          ),
          1 => 
          array (
            0 => 'note_id',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3939 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'debug.pdf.page',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'page',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      3992 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'mail-attachments.download',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4008 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'mail-attachments.preview',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4046 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'mail-attachments.download-all',
          ),
          1 => 
          array (
            0 => 'mailReportId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4074 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'forms.show',
          ),
          1 => 
          array (
            0 => 'form',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4094 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'forms.preview',
          ),
          1 => 
          array (
            0 => 'form',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4105 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'forms.pdf',
          ),
          1 => 
          array (
            0 => 'form',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4146 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.getClientMatters',
          ),
          1 => 
          array (
            0 => 'clientId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4193 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'upload_checklists.matter',
          ),
          1 => 
          array (
            0 => 'matterId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4241 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::mBoXKpHuDMgjgMLO',
          ),
          1 => 
          array (
            0 => 'status',
            1 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4274 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'clients.email.verify',
          ),
          1 => 
          array (
            0 => 'token',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4324 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4344 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.update',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4367 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.json',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4399 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.update-status',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4435 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.update-consultant',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4469 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.update-datetime',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4496 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.add-note',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4530 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.update-followup',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4562 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.send-reminder',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4618 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'booking.appointments.calendar',
          ),
          1 => 
          array (
            0 => 'type',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4657 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.show',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4678 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.reminder',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4691 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.send',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4710 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.copy-link',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4728 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.associate',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4767 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.client-matters',
          ),
          1 => 
          array (
            0 => 'clientId',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4791 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'signatures.detach',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      4819 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'public.documents.sign',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'token',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4863 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::GtzEf9K9QYpzoaTm',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      4891 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::lkGU6cDtylzrvQ5A',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'passport.token' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/token',
      'action' => 
      array (
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\AccessTokenController@issueToken',
        'as' => 'passport.token',
        'middleware' => 'throttle',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\AccessTokenController@issueToken',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.authorizations.authorize' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/authorize',
      'action' => 
      array (
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizationController@authorize',
        'as' => 'passport.authorizations.authorize',
        'middleware' => 'web',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizationController@authorize',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.token.refresh' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/token/refresh',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\TransientTokenController@refresh',
        'as' => 'passport.token.refresh',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\TransientTokenController@refresh',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.authorizations.approve' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/authorize',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ApproveAuthorizationController@approve',
        'as' => 'passport.authorizations.approve',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ApproveAuthorizationController@approve',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.authorizations.deny' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'oauth/authorize',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\DenyAuthorizationController@deny',
        'as' => 'passport.authorizations.deny',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\DenyAuthorizationController@deny',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.tokens.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/tokens',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController@forUser',
        'as' => 'passport.tokens.index',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController@forUser',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.tokens.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'oauth/tokens/{token_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController@destroy',
        'as' => 'passport.tokens.destroy',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\AuthorizedAccessTokenController@destroy',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.clients.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/clients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@forUser',
        'as' => 'passport.clients.index',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@forUser',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.clients.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/clients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@store',
        'as' => 'passport.clients.store',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@store',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.clients.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'oauth/clients/{client_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@update',
        'as' => 'passport.clients.update',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@update',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.clients.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'oauth/clients/{client_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@destroy',
        'as' => 'passport.clients.destroy',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ClientController@destroy',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.scopes.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/scopes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\ScopeController@all',
        'as' => 'passport.scopes.index',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\ScopeController@all',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.personal.tokens.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'oauth/personal-access-tokens',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@forUser',
        'as' => 'passport.personal.tokens.index',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@forUser',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.personal.tokens.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'oauth/personal-access-tokens',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@store',
        'as' => 'passport.personal.tokens.store',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@store',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'passport.personal.tokens.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'oauth/personal-access-tokens/{token_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@destroy',
        'as' => 'passport.personal.tokens.destroy',
        'controller' => 'Laravel\\Passport\\Http\\Controllers\\PersonalAccessTokenController@destroy',
        'namespace' => 'Laravel\\Passport\\Http\\Controllers',
        'prefix' => 'oauth',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'sanctum.csrf-cookie' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'sanctum/csrf-cookie',
      'action' => 
      array (
        'uses' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'controller' => 'Laravel\\Sanctum\\Http\\Controllers\\CsrfCookieController@show',
        'namespace' => NULL,
        'prefix' => 'sanctum',
        'where' => 
        array (
        ),
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'sanctum.csrf-cookie',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::X5uJobyCltR9CX4y' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'captcha/api/{config?}',
      'action' => 
      array (
        'uses' => '\\Mews\\Captcha\\CaptchaController@getCaptchaApi',
        'controller' => '\\Mews\\Captcha\\CaptchaController@getCaptchaApi',
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'generated::X5uJobyCltR9CX4y',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::IgCPOVg8Yp9lTBlS' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'captcha/{config?}',
      'action' => 
      array (
        'uses' => '\\Mews\\Captcha\\CaptchaController@getCaptcha',
        'controller' => '\\Mews\\Captcha\\CaptchaController@getCaptcha',
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'generated::IgCPOVg8Yp9lTBlS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ignition.healthCheck' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '_ignition/health-check',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'Spatie\\LaravelIgnition\\Http\\Middleware\\RunnableSolutionsEnabled',
        ),
        'uses' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\HealthCheckController@__invoke',
        'controller' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\HealthCheckController',
        'as' => 'ignition.healthCheck',
        'namespace' => NULL,
        'prefix' => '_ignition',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ignition.executeSolution' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => '_ignition/execute-solution',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'Spatie\\LaravelIgnition\\Http\\Middleware\\RunnableSolutionsEnabled',
        ),
        'uses' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\ExecuteSolutionController@__invoke',
        'controller' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\ExecuteSolutionController',
        'as' => 'ignition.executeSolution',
        'namespace' => NULL,
        'prefix' => '_ignition',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'ignition.updateConfig' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => '_ignition/update-config',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'Spatie\\LaravelIgnition\\Http\\Middleware\\RunnableSolutionsEnabled',
        ),
        'uses' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\UpdateConfigController@__invoke',
        'controller' => 'Spatie\\LaravelIgnition\\Http\\Controllers\\UpdateConfigController',
        'as' => 'ignition.updateConfig',
        'namespace' => NULL,
        'prefix' => '_ignition',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::KcSLUQvF7REOEAFC' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@login',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@login',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::KcSLUQvF7REOEAFC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8zlZ3xblITPBDip3' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/admin-login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@adminLogin',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@adminLogin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::8zlZ3xblITPBDip3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::gkZsTsSHp8a8mpLN' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/refresh',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@refresh',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@refresh',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::gkZsTsSHp8a8mpLN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::L0flMELwEVPj2krI' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/forgot-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@forgotPassword',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@forgotPassword',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::L0flMELwEVPj2krI',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::rMIdRUpBlHse5Qzu' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/reset-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@resetPassword',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@resetPassword',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::rMIdRUpBlHse5Qzu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::pUaHCFBZp7bzBoeV' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/countries',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalCommonListingController@getCountries',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalCommonListingController@getCountries',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::pUaHCFBZp7bzBoeV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::yE1oR3jFZW57OUL8' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/visa-types',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalCommonListingController@getVisaTypes',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalCommonListingController@getVisaTypes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::yE1oR3jFZW57OUL8',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::t64H9m5hUbnKwqv4' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/search-occupation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalCommonListingController@searchOccupationDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalCommonListingController@searchOccupationDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::t64H9m5hUbnKwqv4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::lClp2zseBsZltVEp' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@logout',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@logout',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::lClp2zseBsZltVEp',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::gPAVZPvWXYOYEPtZ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/logout-all',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@logoutAll',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@logoutAll',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::gPAVZPvWXYOYEPtZ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::cm3Agdt5G2vHh0yO' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@getProfile',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@getProfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::cm3Agdt5G2vHh0yO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Nj9CbBrkQc90XAph' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@updateProfile',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@updateProfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::Nj9CbBrkQc90XAph',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FSa8Zss5ES7p2j7t' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalController@updatePassword',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalController@updatePassword',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::FSa8Zss5ES7p2j7t',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YEpFLkkciSACYISn' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@dashboard',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@dashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::YEpFLkkciSACYISn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::l1xJphkDvFEGxkZe' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/recent-cases',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@recentCaseViewAll',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@recentCaseViewAll',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::l1xJphkDvFEGxkZe',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vB2WE8RsNZug4oVS' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/documents',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@documentViewAll',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@documentViewAll',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::vB2WE8RsNZug4oVS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::XS32lWEBO2TjJLQY' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/upcoming-deadlines',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@upcomingDeadlinesViewAll',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@upcomingDeadlinesViewAll',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::XS32lWEBO2TjJLQY',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Cu9O63NG6fCSHykE' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/recent-activity',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@recentActivityViewAll',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@recentActivityViewAll',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::Cu9O63NG6fCSHykE',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jsp6tjpZaCd6CxxO' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/matters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@getAllMatters',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDashboardController@getAllMatters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::jsp6tjpZaCd6CxxO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::CpV6slETIrLKvzQb' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/get-client-personal-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@getClientPersonalDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@getClientPersonalDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::CpV6slETIrLKvzQb',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::pk8fOlvDXwLRjlDt' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-client-basic-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientBasicDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientBasicDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::pk8fOlvDXwLRjlDt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WFtl0kpvMOjsTC3Y' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-client-phone-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientPhoneDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientPhoneDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::WFtl0kpvMOjsTC3Y',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EVZ5StmKhqp2RSQ2' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-client-email-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientEmailDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientEmailDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::EVZ5StmKhqp2RSQ2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PoFdOXeYCVSymWgn' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-client-address-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientAddressDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientAddressDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::PoFdOXeYCVSymWgn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ME28gdlaua0yOJGo' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-client-travel-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientTravelDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientTravelDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::ME28gdlaua0yOJGo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::cPcvwPB62WMZMUlT' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-client-qualification-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientQualificationDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientQualificationDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::cPcvwPB62WMZMUlT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DRasSzBRYN4Y8zJY' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-client-experience-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientExperienceDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientExperienceDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::DRasSzBRYN4Y8zJY',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::X0dKF2ITijWCEo9q' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-client-occupation-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientOccupationDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientOccupationDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::X0dKF2ITijWCEo9q',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Um8gOUqICBhwMKai' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-client-testscore-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientTestScoreDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientTestScoreDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::Um8gOUqICBhwMKai',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::2HlTOugzwk5W9FXC' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-client-passport-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientPassportDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientPassportDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::2HlTOugzwk5W9FXC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UOYK4F9K0i1DUH0P' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/delete-client-tab-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@deleteClientTabDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@deleteClientTabDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::UOYK4F9K0i1DUH0P',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vmjTVIC0fppTm8R7' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/delete-client-passport-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@deleteClientPassportDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@deleteClientPassportDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::vmjTVIC0fppTm8R7',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7TVz0tYWdgflPI9B' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/update-client-visa-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientVisaDetail',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalPersonalDetailsController@updateClientVisaDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::7TVz0tYWdgflPI9B',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::yGdWiqwGuukqoK28' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/documents/personal/categories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getPersonalDocumentCategories',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getPersonalDocumentCategories',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::yGdWiqwGuukqoK28',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5IJXkbFSHVUwSghu' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/documents/personal/checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getPersonalDocumentChecklist',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getPersonalDocumentChecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::5IJXkbFSHVUwSghu',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Arhm7tU0WbkeCEZQ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/documents/visa/categories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getVisaDocumentCategories',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getVisaDocumentCategories',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::Arhm7tU0WbkeCEZQ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::MG9Gt1NOnjw6oTxI' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/documents/visa/checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getVisaDocumentChecklist',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@getVisaDocumentChecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::MG9Gt1NOnjw6oTxI',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qgcmEfgQvjCSpdnG' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/documents/checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@addDocumentChecklist',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@addDocumentChecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::qgcmEfgQvjCSpdnG',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vHDJfti45dYmXREI' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/documents/upload',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@uploadDocument',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalDocumentController@uploadDocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::vHDJfti45dYmXREI',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Ikc0icWHbwANcBMe' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/workflow/stages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@getWorkflowStages',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@getWorkflowStages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::Ikc0icWHbwANcBMe',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::c26i61V5G3Jwjl3x' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/workflow/stages/{stage_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@getWorkflowStageDetails',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@getWorkflowStageDetails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::c26i61V5G3Jwjl3x',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::sXByQr2UtoblTcj8' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/workflow/allowed-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@allowedChecklistForStages',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@allowedChecklistForStages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::sXByQr2UtoblTcj8',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Be7dzFng1giazbtj' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/workflow/upload-allowed-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@uploadAllowedChecklistDocument',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalWorkflowController@uploadAllowedChecklistDocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::Be7dzFng1giazbtj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NfuA4hibpGGKvic5' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/messages/send',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@sendMessage',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@sendMessage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::NfuA4hibpGGKvic5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::QAGzcDc49CS0KdPc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/messages/send-to-client',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@sendMessageToClient',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@sendMessageToClient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::QAGzcDc49CS0KdPc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Y4VoCD0IlQaD25Sw' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@getMessages',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@getMessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::Y4VoCD0IlQaD25Sw',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nAu36EEDKqAKEYVe' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/messages/unread-count',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@getUnreadCount',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@getUnreadCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::nAu36EEDKqAKEYVe',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::0WirUH1Ek4xPcAtA' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/messages/{id}/read',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@markAsRead',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@markAsRead',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::0WirUH1Ek4xPcAtA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kX0oBm3ovEJBTxs2' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/messages/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@getMessageDetails',
        'controller' => 'App\\Http\\Controllers\\API\\ClientPortalMessageController@getMessageDetails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::kX0oBm3ovEJBTxs2',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::R1Qv07pDn8Yop8U4' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/payments/create-payment-intent',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:2951:"function (\\Illuminate\\Http\\Request $request) {
        $validated = $request->validate([
            \'amount\' => [\'required\', \'integer\', \'min:50\'],
            \'currency\' => [\'sometimes\', \'string\', \'size:3\'],
            \'customer\' => [\'sometimes\', \'string\'],
            \'description\' => [\'sometimes\', \'string\', \'max:255\'],
            \'metadata\' => [\'sometimes\', \'array\'],
            \'receipt_email\' => [\'sometimes\', \'email\'],
            \'automatic_payment_methods.enabled\' => [\'sometimes\', \'boolean\'],
        ]);

        try {
            $stripeSecret = \\config(\'services.stripe.secret\');

            if (!$stripeSecret) {
                return \\response()->json([
                    \'message\' => \'Stripe secret key is not configured.\',
                ], 500);
            }

            $stripe = new \\Stripe\\StripeClient($stripeSecret);

            $payload = [
                \'amount\' => $validated[\'amount\'],
                \'currency\' => \\strtolower($validated[\'currency\'] ?? \'usd\'),
                \'automatic_payment_methods\' => [
                    \'enabled\' => \\data_get($validated, \'automatic_payment_methods.enabled\', true),
                ],
            ];

            if (isset($validated[\'customer\'])) {
                $payload[\'customer\'] = $validated[\'customer\'];
            }

            if (isset($validated[\'description\'])) {
                $payload[\'description\'] = $validated[\'description\'];
            }

            if (isset($validated[\'metadata\'])) {
                $payload[\'metadata\'] = $validated[\'metadata\'];
            }

            if (isset($validated[\'receipt_email\'])) {
                $payload[\'receipt_email\'] = $validated[\'receipt_email\'];
            }

            $paymentIntent = $stripe->paymentIntents->create($payload);

            return \\response()->json([
                \'id\' => $paymentIntent->id,
                \'status\' => $paymentIntent->status,
                \'client_secret\' => $paymentIntent->client_secret,
                \'amount\' => $paymentIntent->amount,
                \'currency\' => $paymentIntent->currency,
            ], 201);
        } catch (\\Stripe\\Exception\\ApiErrorException $exception) {
            \\Illuminate\\Support\\Facades\\Log::error(\'Stripe PaymentIntent creation failed\', [
                \'message\' => $exception->getMessage(),
            ]);

            return \\response()->json([
                \'message\' => \'Unable to create payment intent.\',
                \'error\' => $exception->getMessage(),
            ], 400);
        } catch (\\Throwable $exception) {
            \\Illuminate\\Support\\Facades\\Log::error(\'Unexpected error creating PaymentIntent\', [
                \'message\' => $exception->getMessage(),
            ]);

            return \\response()->json([
                \'message\' => \'An unexpected error occurred.\',
            ], 500);
        }
    }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000a3d0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::R1Qv07pDn8Yop8U4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LBR4HhD2Fh8UnIE1' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/notifications/broadcasts/unread',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\BroadcastNotificationController@unread',
        'controller' => 'App\\Http\\Controllers\\API\\BroadcastNotificationController@unread',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::LBR4HhD2Fh8UnIE1',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::n6aQtvKnOHCpQcYL' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/notifications/broadcasts',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\BroadcastNotificationController@store',
        'controller' => 'App\\Http\\Controllers\\API\\BroadcastNotificationController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::n6aQtvKnOHCpQcYL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Q89fEfv2CIeQ7ezC' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/notifications/broadcasts',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\BroadcastNotificationController@index',
        'controller' => 'App\\Http\\Controllers\\API\\BroadcastNotificationController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::Q89fEfv2CIeQ7ezC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::vWWYnj51hWanJ6Z4' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/notifications/broadcasts/{batchUuid}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\BroadcastNotificationController@show',
        'controller' => 'App\\Http\\Controllers\\API\\BroadcastNotificationController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::vWWYnj51hWanJ6Z4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4qnSBlogC0wexf3y' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/notifications/broadcasts/{notificationId}/read',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
          1 => 'auth:sanctum',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\BroadcastNotificationController@markAsRead',
        'controller' => 'App\\Http\\Controllers\\API\\BroadcastNotificationController@markAsRead',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::4qnSBlogC0wexf3y',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::xE0jhhWlV5F8o2tU' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/broadcasting/auth',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:5710:"function (\\Illuminate\\Http\\Request $request) {
    try {
        // Get the authorization header
        $authHeader = $request->header(\'Authorization\');
        
        if (!$authHeader || !\\str_starts_with($authHeader, \'Bearer \')) {
            return \\response()->json([\'error\' => \'Unauthorized\'], 401);
        }

        // Extract token
        $token = \\substr($authHeader, 7);
        
        // Get request data - handle both form and JSON content types
        $socketId = $request->input(\'socket_id\');
        $channelName = $request->input(\'channel_name\');
        
        // Validate token using Sanctum
        $user = \\Laravel\\Sanctum\\PersonalAccessToken::findToken($token)?->tokenable;
        
        if (!$user) {
            \\Illuminate\\Support\\Facades\\Log::error(\'Invalid token provided for channel auth\', [\'token\' => \\substr($token, 0, 10) . \'...\']);
            return \\response()->json([\'error\' => \'Invalid token\'], 401);
        }
        
        // Log the request details for debugging
        \\Illuminate\\Support\\Facades\\Log::info(\'Broadcasting auth request\', [
            \'content_type\' => $request->header(\'Content-Type\'),
            \'socket_id\' => $socketId,
            \'channel_name\' => $channelName,
            \'user_id\' => $user->id,
            \'request_data\' => $request->all()
        ]);
        
        // Set the authenticated user for the request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        
        // Validate channel name format and authorization
        if (!\\preg_match(\'/^private-(user|matter)\\.\\d+$/\', $channelName)) {
            \\Illuminate\\Support\\Facades\\Log::warning(\'Invalid channel format\', [\'user_id\' => $user->id, \'channel\' => $channelName]);
            return \\response()->json([\'error\' => \'Invalid channel format\'], 403);
        }
        
        // Ensure we have required parameters
        if (!$socketId || !$channelName) {
            \\Illuminate\\Support\\Facades\\Log::warning(\'Missing required parameters\', [
                \'socket_id\' => $socketId,
                \'channel_name\' => $channelName,
                \'user_id\' => $user->id
            ]);
            return \\response()->json([\'error\' => \'Missing required parameters\'], 400);
        }
        
        // Check channel authorization based on channel type
        if (\\str_starts_with($channelName, \'private-user.\')) {
            $requestedUserId = (int) \\substr($channelName, 13); // Remove \'private-user.\'
            if ($user->id !== $requestedUserId) {
                \\Illuminate\\Support\\Facades\\Log::warning(\'User cannot access another user\\\'s channel\', [
                    \'user_id\' => $user->id, 
                    \'requested_user_id\' => $requestedUserId,
                    \'channel\' => $channelName
                ]);
                return \\response()->json([\'error\' => \'Channel access denied\'], 403);
            }
        } elseif (\\str_starts_with($channelName, \'private-matter.\')) {
            $matterId = (int) \\substr($channelName, 15); // Remove \'private-matter.\'
            
            // Check if user is associated with this matter or is superadmin
            $isAssociated = \\Illuminate\\Support\\Facades\\DB::table(\'client_matters\')
                ->where(\'id\', $matterId)
                ->where(function($query) use ($user) {
                    $query->where(\'sel_migration_agent\', $user->id)
                          ->orWhere(\'sel_person_responsible\', $user->id)
                          ->orWhere(\'sel_person_assisting\', $user->id);
                })
                ->exists();
            
            $isSuperAdmin = $user->role == 1;
            
            if (!$isAssociated && !$isSuperAdmin) {
                \\Illuminate\\Support\\Facades\\Log::warning(\'User cannot access matter channel\', [
                    \'user_id\' => $user->id, 
                    \'matter_id\' => $matterId,
                    \'channel\' => $channelName
                ]);
                return \\response()->json([\'error\' => \'Channel access denied\'], 403);
            }
        }
        
        \\Illuminate\\Support\\Facades\\Log::info(\'Channel auth successful\', [\'user_id\' => $user->id, \'channel\' => $channelName]);

        // Generate auth response using Pusher Cloud
        $pusher = new \\Pusher\\Pusher(
            \\config(\'broadcasting.connections.pusher.key\'),
            \\config(\'broadcasting.connections.pusher.secret\'),
            \\config(\'broadcasting.connections.pusher.app_id\'),
            [
                \'cluster\' => \\config(\'broadcasting.connections.pusher.options.cluster\'),
                \'useTLS\' => \\config(\'broadcasting.connections.pusher.options.useTLS\', true),
                \'encrypted\' => \\config(\'broadcasting.connections.pusher.options.encrypted\', true),
            ]
        );

        $authResponse = $pusher->authorizeChannel($channelName, $socketId);

        return \\response($authResponse, 200, [
            \'Content-Type\' => \'text/plain\'
        ]);
        
    } catch (\\Exception $e) {
        \\Illuminate\\Support\\Facades\\Log::error(\'Broadcasting auth error: \' . $e->getMessage(), [
            \'trace\' => $e->getTraceAsString(),
            \'request_data\' => [
                \'socket_id\' => $request->input(\'socket_id\'),
                \'channel_name\' => $request->input(\'channel_name\'),
                \'auth_header\' => $request->header(\'Authorization\') ? \'Present\' : \'Missing\'
            ]
        ]);
        return \\response()->json([\'error\' => \'Authentication failed: \' . $e->getMessage()], 500);
    }
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000a120000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::xE0jhhWlV5F8o2tU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::etQqYtawqbDH778h' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/service-account/generate-token',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'api',
        ),
        'uses' => 'App\\Http\\Controllers\\API\\ServiceAccountController@generateToken',
        'controller' => 'App\\Http\\Controllers\\API\\ServiceAccountController@generateToken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'api',
        'where' => 
        array (
        ),
        'as' => 'generated::etQqYtawqbDH778h',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::E5BoGm97jCBIDX69' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '/',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:60:"function() {
    return \\redirect()->route(\'crm.login\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000a460000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::E5BoGm97jCBIDX69',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Cw2139nlvwpMHTXo' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clear-cache',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:279:"function() {
    \\Artisan::call(\'config:clear\');
    \\Artisan::call(\'view:clear\');
    \\Artisan::call(\'route:clear\');
    \\Artisan::call(\'route:cache\');
    return \\response()->json([
        \'success\' => true,
        \'message\' => \'Cache cleared successfully\'
    ]);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000a480000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Cw2139nlvwpMHTXo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'exception.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'exception',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\ExceptionController@index',
        'controller' => 'App\\Http\\Controllers\\ExceptionController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'exception.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'exception.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'exception',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\ExceptionController@index',
        'controller' => 'App\\Http\\Controllers\\ExceptionController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'exception.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matter.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@index',
        'as' => 'adminconsole.features.matter.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matter.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@create',
        'as' => 'adminconsole.features.matter.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matter.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/matter/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@store',
        'as' => 'adminconsole.features.matter.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matter.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@edit',
        'as' => 'adminconsole.features.matter.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matter.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/matter/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterController@update',
        'as' => 'adminconsole.features.matter.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.tags.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/tags',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TagController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TagController@index',
        'as' => 'adminconsole.features.tags.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.tags.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/tags/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TagController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TagController@create',
        'as' => 'adminconsole.features.tags.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.tags.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/tags/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TagController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TagController@store',
        'as' => 'adminconsole.features.tags.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.tags.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/tags/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TagController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TagController@edit',
        'as' => 'adminconsole.features.tags.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.tags.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/tags/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TagController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TagController@update',
        'as' => 'adminconsole.features.tags.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emaillabels.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/email-labels',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailLabelController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailLabelController@index',
        'as' => 'adminconsole.features.emaillabels.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emaillabels.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/email-labels/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailLabelController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailLabelController@create',
        'as' => 'adminconsole.features.emaillabels.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emaillabels.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/email-labels/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailLabelController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailLabelController@store',
        'as' => 'adminconsole.features.emaillabels.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emaillabels.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/email-labels/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailLabelController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailLabelController@edit',
        'as' => 'adminconsole.features.emaillabels.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emaillabels.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/email-labels/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailLabelController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailLabelController@update',
        'as' => 'adminconsole.features.emaillabels.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/workflow',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@index',
        'as' => 'adminconsole.features.workflow.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/workflow/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@create',
        'as' => 'adminconsole.features.workflow.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/workflow/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@store',
        'as' => 'adminconsole.features.workflow.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/workflow/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@edit',
        'as' => 'adminconsole.features.workflow.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/workflow/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@update',
        'as' => 'adminconsole.features.workflow.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.deactivate' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/workflow/deactivate-workflow/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@deactivateWorkflow',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@deactivateWorkflow',
        'as' => 'adminconsole.features.workflow.deactivate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.workflow.activate' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/workflow/activate-workflow/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@activateWorkflow',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\WorkflowController@activateWorkflow',
        'as' => 'adminconsole.features.workflow.activate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emails.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/emails',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@index',
        'as' => 'adminconsole.features.emails.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emails.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/emails/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@create',
        'as' => 'adminconsole.features.emails.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emails.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/emails/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@store',
        'as' => 'adminconsole.features.emails.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emails.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/emails/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@edit',
        'as' => 'adminconsole.features.emails.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.emails.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/emails/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\EmailController@update',
        'as' => 'adminconsole.features.emails.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.crmemailtemplate.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/crm-email-template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@index',
        'as' => 'adminconsole.features.crmemailtemplate.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.crmemailtemplate.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/crm-email-template/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@create',
        'as' => 'adminconsole.features.crmemailtemplate.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.crmemailtemplate.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/crm-email-template/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@store',
        'as' => 'adminconsole.features.crmemailtemplate.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.crmemailtemplate.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/crm-email-template/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@edit',
        'as' => 'adminconsole.features.crmemailtemplate.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.crmemailtemplate.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/crm-email-template/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\CrmEmailTemplateController@update',
        'as' => 'adminconsole.features.crmemailtemplate.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matteremailtemplate.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter-email-template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@index',
        'as' => 'adminconsole.features.matteremailtemplate.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matteremailtemplate.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter-email-template/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@create',
        'as' => 'adminconsole.features.matteremailtemplate.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matteremailtemplate.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/matter-email-template/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@store',
        'as' => 'adminconsole.features.matteremailtemplate.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matteremailtemplate.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter-email-template/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@edit',
        'as' => 'adminconsole.features.matteremailtemplate.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matteremailtemplate.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/matter-email-template/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterEmailTemplateController@update',
        'as' => 'adminconsole.features.matteremailtemplate.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matterotheremailtemplate.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter-other-email-template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@index',
        'as' => 'adminconsole.features.matterotheremailtemplate.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matterotheremailtemplate.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter-other-email-template/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@create',
        'as' => 'adminconsole.features.matterotheremailtemplate.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matterotheremailtemplate.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/matter-other-email-template/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@store',
        'as' => 'adminconsole.features.matterotheremailtemplate.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matterotheremailtemplate.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/matter-other-email-template/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@edit',
        'as' => 'adminconsole.features.matterotheremailtemplate.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.matterotheremailtemplate.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/matter-other-email-template/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\MatterOtherEmailTemplateController@update',
        'as' => 'adminconsole.features.matterotheremailtemplate.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.personaldocumenttype.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/personal-document-type',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@index',
        'as' => 'adminconsole.features.personaldocumenttype.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.personaldocumenttype.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/personal-document-type/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@create',
        'as' => 'adminconsole.features.personaldocumenttype.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.personaldocumenttype.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/personal-document-type/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@store',
        'as' => 'adminconsole.features.personaldocumenttype.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.personaldocumenttype.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/personal-document-type/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@edit',
        'as' => 'adminconsole.features.personaldocumenttype.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.personaldocumenttype.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/personal-document-type/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@update',
        'as' => 'adminconsole.features.personaldocumenttype.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/personal-document-type/checkcreatefolder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@checkcreatefolder',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\PersonalDocumentTypeController@checkcreatefolder',
        'as' => 'adminconsole.features.',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.visadocumenttype.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/visa-document-type',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@index',
        'as' => 'adminconsole.features.visadocumenttype.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.visadocumenttype.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/visa-document-type/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@create',
        'as' => 'adminconsole.features.visadocumenttype.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.visadocumenttype.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/visa-document-type/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@store',
        'as' => 'adminconsole.features.visadocumenttype.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.visadocumenttype.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/visa-document-type/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@edit',
        'as' => 'adminconsole.features.visadocumenttype.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.visadocumenttype.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/visa-document-type/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@update',
        'as' => 'adminconsole.features.visadocumenttype.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.generated::4y1nsuhm2M8AQM2i' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/visa-document-type/checkcreatefolder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@checkcreatefolder',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\VisaDocumentTypeController@checkcreatefolder',
        'as' => 'adminconsole.features.generated::4y1nsuhm2M8AQM2i',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.documentchecklist.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/document-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@index',
        'as' => 'adminconsole.features.documentchecklist.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.documentchecklist.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/document-checklist/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@create',
        'as' => 'adminconsole.features.documentchecklist.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.documentchecklist.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/document-checklist/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@store',
        'as' => 'adminconsole.features.documentchecklist.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.documentchecklist.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/document-checklist/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@edit',
        'as' => 'adminconsole.features.documentchecklist.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.documentchecklist.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/features/document-checklist/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\DocumentChecklistController@update',
        'as' => 'adminconsole.features.documentchecklist.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@dashboard',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@dashboard',
        'as' => 'adminconsole.features.sms.dashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/history',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@history',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@history',
        'as' => 'adminconsole.features.sms.history',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.history.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/history/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@show',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@show',
        'as' => 'adminconsole.features.sms.history.show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.statistics' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/statistics',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@statistics',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@statistics',
        'as' => 'adminconsole.features.sms.statistics',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.status.check' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/status/{smsLogId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@checkStatus',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsController@checkStatus',
        'as' => 'adminconsole.features.sms.status.check',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.send.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/send',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@create',
        'as' => 'adminconsole.features.sms.send.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.send' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/sms/send',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@send',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@send',
        'as' => 'adminconsole.features.sms.send',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.send.template' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/sms/send/template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@sendFromTemplate',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@sendFromTemplate',
        'as' => 'adminconsole.features.sms.send.template',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.send.bulk' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/sms/send/bulk',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@sendBulk',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsSendController@sendBulk',
        'as' => 'adminconsole.features.sms.send.bulk',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.index',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/templates/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.create',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/features/sms/templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.store',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/templates/{template}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.show',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@show',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/templates/{template}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.edit',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'adminconsole/features/sms/templates/{template}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.update',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'adminconsole/features/sms/templates/{template}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'adminconsole.features.sms.templates.destroy',
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@destroy',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@destroy',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.sms.templates.active' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/sms/templates-active',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@active',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsTemplateController@active',
        'as' => 'adminconsole.features.sms.templates.active',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.esignature.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/esignature',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\ESignatureController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\ESignatureController@index',
        'as' => 'adminconsole.features.esignature.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/esignature',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.features.esignature.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/features/esignature/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\ESignatureController@exportAudit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\ESignatureController@exportAudit',
        'as' => 'adminconsole.features.esignature.export',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/features/esignature',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@index',
        'as' => 'adminconsole.system.users.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@create',
        'as' => 'adminconsole.system.users.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/system/users/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@store',
        'as' => 'adminconsole.system.users.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@edit',
        'as' => 'adminconsole.system.users.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.view' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/view/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@view',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@view',
        'as' => 'adminconsole.system.users.view',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/system/users/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@update',
        'as' => 'adminconsole.system.users.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/system/users/savezone',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@savezone',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@savezone',
        'as' => 'adminconsole.system.',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.active' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/active',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@active',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@active',
        'as' => 'adminconsole.system.users.active',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.inactive' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/inactive',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@inactive',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@inactive',
        'as' => 'adminconsole.system.users.inactive',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.invited' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/invited',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@invited',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@invited',
        'as' => 'adminconsole.system.users.invited',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.clientlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/clientlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@clientlist',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@clientlist',
        'as' => 'adminconsole.system.users.clientlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.createclient' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/createclient',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@createclient',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@createclient',
        'as' => 'adminconsole.system.users.createclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.storeclient' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/system/users/storeclient',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@storeclient',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@storeclient',
        'as' => 'adminconsole.system.users.storeclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.editclient' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/users/editclient/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@editclient',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@editclient',
        'as' => 'adminconsole.system.users.editclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.users.updateclient' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/system/users/client/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserController@updateclient',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserController@updateclient',
        'as' => 'adminconsole.system.users.updateclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.roles.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/roles',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@index',
        'as' => 'adminconsole.system.roles.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.roles.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/roles/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@create',
        'as' => 'adminconsole.system.roles.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.roles.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/system/roles/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@store',
        'as' => 'adminconsole.system.roles.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.roles.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/roles/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@edit',
        'as' => 'adminconsole.system.roles.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.roles.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/system/roles/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\UserroleController@update',
        'as' => 'adminconsole.system.roles.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.teams.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/teams',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@index',
        'as' => 'adminconsole.system.teams.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.teams.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/teams/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@edit',
        'as' => 'adminconsole.system.teams.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.teams.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/system/teams/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@store',
        'as' => 'adminconsole.system.teams.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.teams.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/system/teams/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\TeamController@update',
        'as' => 'adminconsole.system.teams.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/offices',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@index',
        'as' => 'adminconsole.system.offices.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/offices/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@create',
        'as' => 'adminconsole.system.offices.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/system/offices/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@store',
        'as' => 'adminconsole.system.offices.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/offices/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@edit',
        'as' => 'adminconsole.system.offices.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.view' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/offices/view/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@view',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@view',
        'as' => 'adminconsole.system.offices.view',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.viewclient' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/offices/view/client/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@viewclient',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@viewclient',
        'as' => 'adminconsole.system.offices.viewclient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.offices.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/system/offices/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\BranchesController@update',
        'as' => 'adminconsole.system.offices.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.system.clientsemaillist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/system/clientsemaillist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientsemaillist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientsemaillist',
        'as' => 'adminconsole.system.clientsemaillist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/system',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/database/anzsco',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@index',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@index',
        'as' => 'adminconsole.database.anzsco.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.data' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/database/anzsco/data',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@getData',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@getData',
        'as' => 'adminconsole.database.anzsco.data',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/database/anzsco/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@create',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@create',
        'as' => 'adminconsole.database.anzsco.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/database/anzsco/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@store',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@store',
        'as' => 'adminconsole.database.anzsco.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/database/anzsco/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@edit',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@edit',
        'as' => 'adminconsole.database.anzsco.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'adminconsole/database/anzsco/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@update',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@update',
        'as' => 'adminconsole.database.anzsco.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.import' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'adminconsole/database/anzsco/import',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@importPage',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@importPage',
        'as' => 'adminconsole.database.anzsco.import',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'adminconsole.database.anzsco.import.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'adminconsole/database/anzsco/import',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@import',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@import',
        'as' => 'adminconsole.database.anzsco.import.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'adminconsole/database',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'crm.login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@showLoginForm',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@showLoginForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'crm.login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'crm.login.post' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@login',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@login',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'crm.login.post',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'crm.logout' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@logout',
        'controller' => 'App\\Http\\Controllers\\Auth\\AdminLoginController@logout',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'crm.logout',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'crm.logout.get' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:60:"function() {
    return \\redirect()->route(\'crm.login\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000a4f0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'crm.logout.get',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.column-preferences' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/column-preferences',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@saveColumnPreferences',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@saveColumnPreferences',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.column-preferences',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.update-stage' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/update-stage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateStage',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateStage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.update-stage',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.extend-deadline' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/extend-deadline',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@extendDeadlineDate',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@extendDeadlineDate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.extend-deadline',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.update-task-completed' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/update-task-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateTaskCompleted',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateTaskCompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.update-task-completed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.fetch-notifications' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard/fetch-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchnotification',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchnotification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.fetch-notifications',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.fetch-office-visit-notifications' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard/fetch-office-visit-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchOfficeVisitNotifications',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchOfficeVisitNotifications',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.fetch-office-visit-notifications',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.mark-notification-seen' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/mark-notification-seen',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@markNotificationSeen',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@markNotificationSeen',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.mark-notification-seen',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.fetch-visa-expiry-messages' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard/fetch-visa-expiry-messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchvisaexpirymessages',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchvisaexpirymessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.fetch-visa-expiry-messages',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.fetch-in-person-waiting-count' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard/fetch-in-person-waiting-count',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchInPersonWaitingCount',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchInPersonWaitingCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.fetch-in-person-waiting-count',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.fetch-total-activity-count' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard/fetch-total-activity-count',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchTotalActivityCount',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchTotalActivityCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.fetch-total-activity-count',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.check-checkin-status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/check-checkin-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@checkCheckinStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@checkCheckinStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.check-checkin-status',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.update-checkin-status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dashboard/update-checkin-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateCheckinStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateCheckinStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.update-checkin-status',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'my_profile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'my_profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@myProfile',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@myProfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'my_profile',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'my_profile.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'my_profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@myProfile',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@myProfile',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'my_profile.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'change_password' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'change_password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@change_password',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@change_password',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'change_password',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'change_password.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'change_password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@change_password',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@change_password',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'change_password.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::SzUno8Gi6Ld4qI3R' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@updateAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@updateAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::SzUno8Gi6Ld4qI3R',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::2RnEyqKNKTi12c5R' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'approved_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@approveAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@approveAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::2RnEyqKNKTi12c5R',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::pA3ydW0Qg2j7NJmQ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'process_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@processAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@processAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::pA3ydW0Qg2j7NJmQ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::xrhxQ9x0VwulBgR6' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'archive_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@archiveAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@archiveAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::xrhxQ9x0VwulBgR6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ovsd7ATm7kZePXxF' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'declined_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@declinedAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@declinedAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::ovsd7ATm7kZePXxF',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::3LEssW439PwEmiuV' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'delete_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@deleteAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@deleteAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::3LEssW439PwEmiuV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9wrWuNagIP1oNRHG' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'move_action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@moveAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@moveAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::9wrWuNagIP1oNRHG',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments-education' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'appointments-education',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsEducation',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsEducation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'appointments-education',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments-jrp' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'appointments-jrp',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsJrp',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsJrp',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'appointments-jrp',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments-tourist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'appointments-tourist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsTourist',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsTourist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'appointments-tourist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments-others' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'appointments-others',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsOthers',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsOthers',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'appointments-others',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'add_ckeditior_image' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'add_ckeditior_image',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@addCkeditiorImage',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@addCkeditiorImage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'add_ckeditior_image',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'get_chapters' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'get_chapters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getChapters',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getChapters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'get_chapters',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::hjZ6BC24RRV7TmrT' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'get_states',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getStates',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getStates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::hjZ6BC24RRV7TmrT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'returnsetting' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'settings/taxes/returnsetting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@returnsetting',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@returnsetting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'returnsetting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'savereturnsetting' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'settings/taxes/savereturnsetting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@returnsetting',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@returnsetting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'savereturnsetting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::LZa8rp5PrmcQuRez' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getsubcategories',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getsubcategories',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getsubcategories',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::LZa8rp5PrmcQuRez',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::51gjSn3Ckem847oQ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getassigneeajax',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getassigneeajax',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getassigneeajax',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::51gjSn3Ckem847oQ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PbvmMHugc10wGjR6' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getpartnerajax',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getpartnerajax',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getpartnerajax',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::PbvmMHugc10wGjR6',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::C3ngxuj7fgCxKmwM' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'checkclientexist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@checkclientexist',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@checkclientexist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::C3ngxuj7fgCxKmwM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'notifications.broadcasts.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'notifications/broadcasts/manage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BroadcastController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\BroadcastController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'notifications.broadcasts.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard.active-users' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard/active-users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ActiveUserController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\ActiveUserController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard.active-users',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'notifications.broadcasts.send' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'notifications/broadcasts/send',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@store',
        'as' => 'notifications.broadcasts.send',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/notifications/broadcasts',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'notifications.broadcasts.history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'notifications/broadcasts/history',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@history',
        'controller' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@history',
        'as' => 'notifications.broadcasts.history',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/notifications/broadcasts',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'notifications.broadcasts.my-history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'notifications/broadcasts/my-history',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@myHistory',
        'controller' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@myHistory',
        'as' => 'notifications.broadcasts.my-history',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/notifications/broadcasts',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'notifications.broadcasts.read-history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'notifications/broadcasts/read-history',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@readHistory',
        'controller' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@readHistory',
        'as' => 'notifications.broadcasts.read-history',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/notifications/broadcasts',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'notifications.broadcasts.unread' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'notifications/broadcasts/unread',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@unread',
        'controller' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@unread',
        'as' => 'notifications.broadcasts.unread',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/notifications/broadcasts',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'notifications.broadcasts.details' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'notifications/broadcasts/{batchUuid}/details',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@details',
        'controller' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@details',
        'as' => 'notifications.broadcasts.details',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/notifications/broadcasts',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'batchUuid' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'notifications.broadcasts.read' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'notifications/broadcasts/{notificationId}/read',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@markAsRead',
        'controller' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@markAsRead',
        'as' => 'notifications.broadcasts.read',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/notifications/broadcasts',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'notificationId' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'notifications.broadcasts.delete' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'notifications/broadcasts/{batchUuid}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@delete',
        'controller' => 'App\\Http\\Controllers\\CRM\\BroadcastNotificationAjaxController@delete',
        'as' => 'notifications.broadcasts.delete',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/notifications/broadcasts',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'batchUuid' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'user-login-analytics.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'user-login-analytics',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'user-login-analytics.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'api.user-login-analytics.daily' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/user-login-analytics/daily',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@daily',
        'controller' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@daily',
        'as' => 'api.user-login-analytics.daily',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/api/user-login-analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'api.user-login-analytics.weekly' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/user-login-analytics/weekly',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@weekly',
        'controller' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@weekly',
        'as' => 'api.user-login-analytics.weekly',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/api/user-login-analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'api.user-login-analytics.monthly' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/user-login-analytics/monthly',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@monthly',
        'controller' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@monthly',
        'as' => 'api.user-login-analytics.monthly',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/api/user-login-analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'api.user-login-analytics.hourly' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/user-login-analytics/hourly',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@hourly',
        'controller' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@hourly',
        'as' => 'api.user-login-analytics.hourly',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/api/user-login-analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'api.user-login-analytics.summary' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/user-login-analytics/summary',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@summary',
        'controller' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@summary',
        'as' => 'api.user-login-analytics.summary',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/api/user-login-analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'api.user-login-analytics.top-users' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/user-login-analytics/top-users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@topUsers',
        'controller' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@topUsers',
        'as' => 'api.user-login-analytics.top-users',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/api/user-login-analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'api.user-login-analytics.trends' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api/user-login-analytics/trends',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@trends',
        'controller' => 'App\\Http\\Controllers\\CRM\\UserLoginAnalyticsController@trends',
        'as' => 'api.user-login-analytics.trends',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/api/user-login-analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@index',
        'as' => 'leads.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@detail',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@detail',
        'as' => 'leads.detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/history/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@history',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@history',
        'as' => 'leads.history',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@create',
        'as' => 'leads.create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@store',
        'as' => 'leads.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/{id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@edit',
        'as' => 'leads.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'leads/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@update',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@update',
        'as' => 'leads.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.patch' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'leads/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@update',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@update',
        'as' => 'leads.patch',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.assign' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/assign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAssignmentController@assign',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAssignmentController@assign',
        'as' => 'leads.assign',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.bulk_assign' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/bulk-assign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAssignmentController@bulkAssign',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAssignmentController@bulkAssign',
        'as' => 'leads.bulk_assign',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.assignable_users' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/assignable-users',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAssignmentController@getAssignableUsers',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAssignmentController@getAssignableUsers',
        'as' => 'leads.assignable_users',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.convert' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/convert',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@convertToClient',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@convertToClient',
        'as' => 'leads.convert',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.convert_single' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/convert-single',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@convertSingleLead',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@convertSingleLead',
        'as' => 'leads.convert_single',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.bulk_convert' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/bulk-convert',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@bulkConvertToClient',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@bulkConvertToClient',
        'as' => 'leads.bulk_convert',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.conversion_stats' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/conversion-stats',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@getConversionStats',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadConversionController@getConversionStats',
        'as' => 'leads.conversion_stats',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.followups.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/followups',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@index',
        'as' => 'leads.followups.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/followups',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.followups.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/followups/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@myFollowups',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@myFollowups',
        'as' => 'leads.followups.dashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/followups',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.followups.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/followups',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@store',
        'as' => 'leads.followups.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/followups',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.followups.complete' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/followups/{id}/complete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@complete',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@complete',
        'as' => 'leads.followups.complete',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/followups',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.followups.reschedule' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/followups/{id}/reschedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@reschedule',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@reschedule',
        'as' => 'leads.followups.reschedule',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/followups',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.followups.cancel' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/followups/{id}/cancel',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@cancel',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@cancel',
        'as' => 'leads.followups.cancel',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/followups',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.followups.get' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/{leadId}/followups',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@getLeadFollowups',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadFollowupController@getLeadFollowups',
        'as' => 'leads.followups.get',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.analytics.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/analytics',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@index',
        'as' => 'leads.analytics.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.analytics.trends' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/analytics/trends',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@getTrends',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@getTrends',
        'as' => 'leads.analytics.trends',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.analytics.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/analytics/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@export',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@export',
        'as' => 'leads.analytics.export',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.analytics.compare' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/analytics/compare-agents',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@compareAgents',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadAnalyticsController@compareAgents',
        'as' => 'leads.analytics.compare',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => 'leads/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.notes.delete' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/notes/delete/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@leaddeleteNotes',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@leaddeleteNotes',
        'as' => 'leads.notes.delete',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.pin' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'leads/pin/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@leadPin',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@leadPin',
        'as' => 'leads.pin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/leads',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'get-notedetail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-notedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@getnotedetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@getnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'get-notedetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'email_templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'email.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'email_templates/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'email.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'email_templates/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'email.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'edit_email_template' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'edit_email_template/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@editEmailTemplate',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@editEmailTemplate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'edit_email_template',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'edit_email_template.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'edit_email_template',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@editEmailTemplate',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailTemplateController@editEmailTemplate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'edit_email_template.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'api' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'api-key',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@editapi',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@editapi',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'api',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'api.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api-key',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@editapi',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@editapi',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'api.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.clientsmatterslist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clientsmatterslist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientsmatterslist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientsmatterslist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.clientsmatterslist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.clientsemaillist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clientsemaillist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientsemaillist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@clientsemaillist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.clientsemaillist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/edit/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveSection' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/save-section',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@saveSection',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@saveSection',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveSection',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.partnerEoiData' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/partner-eoi-data/{partnerId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getPartnerEoiData',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getPartnerEoiData',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.partnerEoiData',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.phone.sendOTP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/phone/send-otp',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@sendOTP',
        'controller' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@sendOTP',
        'as' => 'clients.phone.sendOTP',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/phone',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.phone.verifyOTP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/phone/verify-otp',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@verifyOTP',
        'controller' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@verifyOTP',
        'as' => 'clients.phone.verifyOTP',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/phone',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.phone.resendOTP' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/phone/resend-otp',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@resendOTP',
        'controller' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@resendOTP',
        'as' => 'clients.phone.resendOTP',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/phone',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.phone.status' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/phone/status/{contactId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@getStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\PhoneVerificationController@getStatus',
        'as' => 'clients.phone.status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/phone',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.email.sendVerification' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/email/send-verification',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@sendVerificationEmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@sendVerificationEmail',
        'as' => 'clients.email.sendVerification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/email',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.email.resendVerification' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/email/resend-verification',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@resendVerificationEmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@resendVerificationEmail',
        'as' => 'clients.email.resendVerification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/email',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.email.status' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/email/status/{emailId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@getStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@getStatus',
        'as' => 'clients.email.status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/email',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EAlTwODOBbUTA42C' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/followup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@followupstore',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@followupstore',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::EAlTwODOBbUTA42C',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7KgtmXSHnWvp0NQm' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/followup/retagfollowup',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@retagfollowup',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@retagfollowup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::7KgtmXSHnWvp0NQm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::2MawSvtbgyA6MDzZ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/changetype/{id}/{type}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@changetype',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@changetype',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::2MawSvtbgyA6MDzZ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::TeRHpxCMo295bRvl' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'document/download/pdf/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@downloadpdf',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@downloadpdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::TeRHpxCMo295bRvl',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ZwASEfzrfflnqMLX' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/removetag',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@removetag',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@removetag',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::ZwASEfzrfflnqMLX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/detail/{client_id}/{client_unique_matter_ref_no?}/{tab?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@detail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getrecipients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/get-recipients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getrecipients',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getrecipients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getrecipients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getonlyclientrecipients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/get-onlyclientrecipients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getonlyclientrecipients',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getonlyclientrecipients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getonlyclientrecipients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getallclients' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/get-allclients',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getallclients',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getallclients',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getallclients',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::SPDN6slJahUwYmLf' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/change_assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@change_assignee',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@change_assignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::SPDN6slJahUwYmLf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.gettemplates' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@gettemplates',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@gettemplates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.gettemplates',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.sendmail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'sendmail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@sendmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@sendmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.sendmail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::MONDBQ6lxQQf2jpr' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'upload-mail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@uploadmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@uploadmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::MONDBQ6lxQQf2jpr',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email.upload.inbox' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'upload-fetch-mail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailUploadController@uploadInboxEmails',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailUploadController@uploadInboxEmails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'email.upload.inbox',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email.upload.sent' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'upload-sent-fetch-mail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailUploadController@uploadSentEmails',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailUploadController@uploadSentEmails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'email.upload.sent',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email.check.service' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'email/check-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailUploadController@checkPythonService',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailUploadController@checkPythonService',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'email.check.service',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.reassiginboxemail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'reassiginboxemail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@reassiginboxemail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@reassiginboxemail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.reassiginboxemail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.reassigsentemail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'reassigsentemail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@reassigsentemail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@reassigsentemail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.reassigsentemail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.listAllMattersWRTSelClient' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'listAllMattersWRTSelClient',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@listAllMattersWRTSelClient',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@listAllMattersWRTSelClient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.listAllMattersWRTSelClient',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updatemailreadbit' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'updatemailreadbit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@updatemailreadbit',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@updatemailreadbit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updatemailreadbit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.filter.emails' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/filter-emails',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@filterEmails',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@filterEmails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.filter.emails',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.filter.sentmails' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/filter-sentemails',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@filterSentEmails',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@filterSentEmails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.filter.sentmails',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'mail.enhance' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'mail/enhance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@enhanceMessage',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@enhanceMessage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'mail.enhance',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email-labels.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'email-labels',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailLabelController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailLabelController@index',
        'as' => 'email-labels.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/email-labels',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email-labels.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'email-labels',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailLabelController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailLabelController@store',
        'as' => 'email-labels.store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/email-labels',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email-labels.apply' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'email-labels/apply',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailLabelController@apply',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailLabelController@apply',
        'as' => 'email-labels.apply',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/email-labels',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'email-labels.remove' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'email-labels/remove',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailLabelController@remove',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailLabelController@remove',
        'as' => 'email-labels.remove',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/email-labels',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'mail-attachments.download' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'mail-attachments/{id}/download',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\MailReportAttachmentController@download',
        'controller' => 'App\\Http\\Controllers\\CRM\\MailReportAttachmentController@download',
        'as' => 'mail-attachments.download',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/mail-attachments',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'mail-attachments.preview' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'mail-attachments/{id}/preview',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\MailReportAttachmentController@preview',
        'controller' => 'App\\Http\\Controllers\\CRM\\MailReportAttachmentController@preview',
        'as' => 'mail-attachments.preview',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/mail-attachments',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'mail-attachments.download-all' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'mail-attachments/email/{mailReportId}/download-all',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\MailReportAttachmentController@downloadAll',
        'controller' => 'App\\Http\\Controllers\\CRM\\MailReportAttachmentController@downloadAll',
        'as' => 'mail-attachments.download-all',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/mail-attachments',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.createnote' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'create-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@createnote',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@createnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.createnote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updateNoteDatetime' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-note-datetime',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@updateNoteDatetime',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@updateNoteDatetime',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updateNoteDatetime',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getnotedetail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getnotedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@getnotedetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@getnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getnotedetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.deletenote' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'deletenote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@deletenote',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@deletenote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.deletenote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::qOqMKTUfZwss5rJX' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'viewnotedetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@viewnotedetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@viewnotedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::qOqMKTUfZwss5rJX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::x3pJ6S3djQ5IQFHP' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'viewapplicationnote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@viewapplicationnote',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@viewapplicationnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::x3pJ6S3djQ5IQFHP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nU8CRpx0aBwrslFD' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'saveprevvisa',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveprevvisa',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveprevvisa',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::nU8CRpx0aBwrslFD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::upsF7grQrBStX3IT' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'saveonlineprimaryform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveonlineform',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveonlineform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::upsF7grQrBStX3IT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EP52I2nJuGD4Z5ow' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'saveonlinesecform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveonlineform',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveonlineform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::EP52I2nJuGD4Z5ow',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4wQRXmUjeZIrNKOW' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'saveonlinechildform',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveonlineform',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@saveonlineform',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::4wQRXmUjeZIrNKOW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getnotes' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-notes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@getnotes',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@getnotes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getnotes',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::xqjy16jHRfj6fX96' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'pinnote',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@pinnote',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientNotesController@pinnote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::xqjy16jHRfj6fX96',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.convertActivityToNote' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'convert-activity-to-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@convertActivityToNote',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@convertActivityToNote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.convertActivityToNote',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.archived' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'archived',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@archived',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@archived',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.archived',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updateclientstatus' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'change-client-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@updateclientstatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@updateclientstatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updateclientstatus',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.activities' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-activities',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@activities',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@activities',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.activities',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.deletecostagreement' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'deletecostagreement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@deletecostagreement',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@deletecostagreement',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.deletecostagreement',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.deleteactivitylog' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'deleteactivitylog',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@deleteactivitylog',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@deleteactivitylog',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.deleteactivitylog',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.notpickedcall' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'not-picked-call',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@notpickedcall',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@notpickedcall',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.notpickedcall',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wMHdBhfRaH1soHaV' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'pinactivitylog',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@pinactivitylog',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@pinactivitylog',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::wMHdBhfRaH1soHaV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::tbknAIiU6iixViIg' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'interested-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@interestedService',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@interestedService',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::tbknAIiU6iixViIg',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::N2OsGCZsiymD4syl' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'edit-interested-service',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@editinterestedService',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@editinterestedService',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::N2OsGCZsiymD4syl',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Bm9V6Pd3cM8XV73R' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-services',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getServices',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getServices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Bm9V6Pd3cM8XV73R',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::BSIheMbRYEwL0jWH' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'servicesavefee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@servicesavefee',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@servicesavefee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::BSIheMbRYEwL0jWH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Qf6vcnHPPuwIvQRi' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getintrestedservice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getintrestedservice',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getintrestedservice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Qf6vcnHPPuwIvQRi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9s1n9mSEbD0O2Xd7' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getintrestedserviceedit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getintrestedserviceedit',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getintrestedserviceedit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::9s1n9mSEbD0O2Xd7',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getapplicationlists' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-application-lists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getapplicationlists',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getapplicationlists',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getapplicationlists',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveapplication' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'saveapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveapplication',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@saveapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveapplication',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.convertapplication' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'convertapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@convertapplication',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@convertapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.convertapplication',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.deleteservices' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'deleteservices',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@deleteservices',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@deleteservices',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.deleteservices',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8mnZXUfWHCPBU7hv' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'savetoapplication',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@savetoapplication',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@savetoapplication',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::8mnZXUfWHCPBU7hv',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Bw15jhUzoV8mnvbt' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'client/createservicetaken',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@createservicetaken',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@createservicetaken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Bw15jhUzoV8mnvbt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::VIO3IfQTLkneNJ9o' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'client/removeservicetaken',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@removeservicetaken',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@removeservicetaken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::VIO3IfQTLkneNJ9o',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Dzmfl1XbyKEJ8WMa' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'client/getservicetaken',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getservicetaken',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getservicetaken',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Dzmfl1XbyKEJ8WMa',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.addedudocchecklist' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/add-edu-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addedudocchecklist',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addedudocchecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.addedudocchecklist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.uploadedudocument' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/upload-edu-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@uploadedudocument',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@uploadedudocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.uploadedudocument',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.addvisadocchecklist' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/add-visa-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addvisadocchecklist',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addvisadocchecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.addvisadocchecklist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.uploadvisadocument' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/upload-visa-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@uploadvisadocument',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@uploadvisadocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.uploadvisadocument',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.renamedoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/rename',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@renamedoc',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@renamedoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.renamedoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.deletedocs' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/delete',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@deletedocs',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@deletedocs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.deletedocs',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.getvisachecklist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/get-visa-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@getvisachecklist',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@getvisachecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.getvisachecklist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.notuseddoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/not-used',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@notuseddoc',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@notuseddoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.notuseddoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.renamechecklistdoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/rename-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@renamechecklistdoc',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@renamechecklistdoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.renamechecklistdoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.deleteChecklist' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/delete-checklist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@deleteChecklist',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@deleteChecklist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.deleteChecklist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.backtodoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/back-to-doc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@backtodoc',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@backtodoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.backtodoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.download' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/download',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@download_document',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@download_document',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.download',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.addPersonalDocCategory' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/add-personal-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addPersonalDocCategory',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addPersonalDocCategory',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.addPersonalDocCategory',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.updatePersonalDocCategory' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/update-personal-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@updatePersonalDocCategory',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@updatePersonalDocCategory',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.updatePersonalDocCategory',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.addVisaDocCategory' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/add-visa-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addVisaDocCategory',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@addVisaDocCategory',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.addVisaDocCategory',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.documents.updateVisaDocCategory' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/update-visa-category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@updateVisaDocCategory',
        'controller' => 'App\\Http\\Controllers\\CRM\\Clients\\ClientDocumentsController@updateVisaDocCategory',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.documents.updateVisaDocCategory',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.eoi-roi.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/{client}/eoi-roi',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@index',
        'as' => 'clients.eoi-roi.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/{client}/eoi-roi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.eoi-roi.calculatePoints' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/{client}/eoi-roi/calculate-points',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@calculatePoints',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@calculatePoints',
        'as' => 'clients.eoi-roi.calculatePoints',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/{client}/eoi-roi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.eoi-roi.upsert' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/{client}/eoi-roi',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@upsert',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@upsert',
        'as' => 'clients.eoi-roi.upsert',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/{client}/eoi-roi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.eoi-roi.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/{client}/eoi-roi/{eoiReference}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@show',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@show',
        'as' => 'clients.eoi-roi.show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/{client}/eoi-roi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.eoi-roi.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'clients/{client}/eoi-roi/{eoiReference}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@destroy',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@destroy',
        'as' => 'clients.eoi-roi.destroy',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/{client}/eoi-roi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.eoi-roi.revealPassword' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/{client}/eoi-roi/{eoiReference}/reveal-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@revealPassword',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientEoiRoiController@revealPassword',
        'as' => 'clients.eoi-roi.revealPassword',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/clients/{client}/eoi-roi',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveaccountreport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/saveaccountreport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveaccountreport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveaccountreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveaccountreport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveaccountreport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/saveaccountreport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveaccountreport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveaccountreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveaccountreport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.test-python-accounting' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/test-python-accounting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@testPythonAccounting',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@testPythonAccounting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.test-python-accounting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveinvoicereport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/saveinvoicereport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveinvoicereport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveinvoicereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveinvoicereport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveinvoicereport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/saveinvoicereport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveinvoicereport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveinvoicereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveinvoicereport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveadjustinvoicereport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/saveadjustinvoicereport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveadjustinvoicereport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveadjustinvoicereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveadjustinvoicereport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveadjustinvoicereport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/saveadjustinvoicereport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveadjustinvoicereport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveadjustinvoicereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveadjustinvoicereport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveofficereport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/saveofficereport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveofficereport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveofficereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveofficereport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveofficereport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/saveofficereport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveofficereport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@saveofficereport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveofficereport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.savejournalreport' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/savejournalreport/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@savejournalreport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@savejournalreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.savejournalreport',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.savejournalreport.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/savejournalreport',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@savejournalreport',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@savejournalreport',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.savejournalreport.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.isAnyInvoiceNoExistInDB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/isAnyInvoiceNoExistInDB',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@isAnyInvoiceNoExistInDB',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@isAnyInvoiceNoExistInDB',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.isAnyInvoiceNoExistInDB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.listOfInvoice' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/listOfInvoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@listOfInvoice',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@listOfInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.listOfInvoice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getTopReceiptValInDB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getTopReceiptValInDB',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@getTopReceiptValInDB',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@getTopReceiptValInDB',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getTopReceiptValInDB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getInfoByReceiptId' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getInfoByReceiptId',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@getInfoByReceiptId',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@getInfoByReceiptId',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getInfoByReceiptId',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ARmwSaeMSy65Z26x' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/genInvoice/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@genInvoice',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@genInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::ARmwSaeMSy65Z26x',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.sendToHubdoc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/sendToHubdoc/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@sendToHubdoc',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@sendToHubdoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.sendToHubdoc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.checkHubdocStatus' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/checkHubdocStatus/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@checkHubdocStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@checkHubdocStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.checkHubdocStatus',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::fYS4GLAgTYfsXegH' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/printPreview/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@printPreview',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@printPreview',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::fYS4GLAgTYfsXegH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getTopInvoiceNoFromDB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getTopInvoiceNoFromDB',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@getTopInvoiceNoFromDB',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@getTopInvoiceNoFromDB',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getTopInvoiceNoFromDB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.clientLedgerBalanceAmount' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/clientLedgerBalanceAmount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@clientLedgerBalanceAmount',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@clientLedgerBalanceAmount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.clientLedgerBalanceAmount',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.analytics-dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/analytics-dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@analyticsDashboard',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@analyticsDashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.analytics-dashboard',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.insights' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/insights',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@insights',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@insights',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.insights',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.invoicelist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/invoicelist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@invoicelist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@invoicelist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.invoicelist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'client.void_invoice' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'void_invoice',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@void_invoice',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@void_invoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'client.void_invoice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.clientreceiptlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/clientreceiptlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@clientreceiptlist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@clientreceiptlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.clientreceiptlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.officereceiptlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/officereceiptlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@officereceiptlist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@officereceiptlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.officereceiptlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.journalreceiptlist' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/journalreceiptlist',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@journalreceiptlist',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@journalreceiptlist',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.journalreceiptlist',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'client.validate_receipt' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'validate_receipt',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@validate_receipt',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@validate_receipt',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'client.validate_receipt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::2IQ88phT5LCfxHZB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'delete_receipt',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@delete_receipt',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@delete_receipt',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::2IQ88phT5LCfxHZB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4GrJApF4iqmAEVsN' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/genClientFundReceipt/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@genClientFundReceipt',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@genClientFundReceipt',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::4GrJApF4iqmAEVsN',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wUw75fgSHQEFOYIc' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/genOfficeReceipt/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@genofficereceiptInvoice',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@genofficereceiptInvoice',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::wUw75fgSHQEFOYIc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.update-client-funds-ledger' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-client-funds-ledger',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@updateClientFundsLedger',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@updateClientFundsLedger',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.update-client-funds-ledger',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updateOfficeReceipt' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-office-receipt',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@updateOfficeReceipt',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@updateOfficeReceipt',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updateOfficeReceipt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getInvoicesByMatter' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'get-invoices-by-matter',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@getInvoicesByMatter',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@getInvoicesByMatter',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getInvoicesByMatter',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updateClientFundLedger' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-client-fund-ledger',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@updateClientFundLedger',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@updateClientFundLedger',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updateClientFundLedger',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.invoiceamount' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/invoiceamount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@getInvoiceAmount',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@getInvoiceAmount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.invoiceamount',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.uploadclientreceiptdocument' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/upload-clientreceipt-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@uploadclientreceiptdocument',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@uploadclientreceiptdocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.uploadclientreceiptdocument',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.uploadofficereceiptdocument' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/upload-officereceipt-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@uploadofficereceiptdocument',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@uploadofficereceiptdocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.uploadofficereceiptdocument',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.uploadjournalreceiptdocument' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/upload-journalreceipt-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@uploadjournalreceiptdocument',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientAccountsController@uploadjournalreceiptdocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.uploadjournalreceiptdocument',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updateAddress' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/update-address',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateAddress',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateAddress',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updateAddress',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.searchAddressFull' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/search-address-full',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@searchAddressFull',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@searchAddressFull',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.searchAddressFull',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getPlaceDetails' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/get-place-details',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getPlaceDetails',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getPlaceDetails',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getPlaceDetails',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::KY7t8BQB08a17Bb3' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'address_auto_populate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@address_auto_populate',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@address_auto_populate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::KY7t8BQB08a17Bb3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::S5zEdfEzxgaMwFCy' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/fetchClientContactNo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@fetchClientContactNo',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@fetchClientContactNo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::S5zEdfEzxgaMwFCy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.clientdetailsinfo' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/clientdetailsinfo/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@clientdetailsinfo',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@clientdetailsinfo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.clientdetailsinfo',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.clientdetailsinfo.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/clientdetailsinfo',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@clientdetailsinfo',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@clientdetailsinfo',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.clientdetailsinfo.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'getVisaTypes' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-visa-types',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getVisaTypes',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getVisaTypes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'getVisaTypes',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'getCountries' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-countries',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getCountries',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@getCountries',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'getCountries',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updateOccupation' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'updateOccupation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateOccupation',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateOccupation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updateOccupation',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'leads.updateOccupation' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'leads/updateOccupation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateOccupation',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateOccupation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'leads.updateOccupation',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.searchPartner' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/search-partner',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@searchPartner',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@searchPartner',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.searchPartner',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.searchPartnerTest' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/search-partner-test',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@searchPartnerTest',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@searchPartnerTest',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.searchPartnerTest',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.testBidirectional' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/test-bidirectional',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@testBidirectionalRemoval',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@testBidirectionalRemoval',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.testBidirectional',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.saveRelationship' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/save-relationship',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@saveRelationship',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@saveRelationship',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.saveRelationship',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.generateagreement' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/generateagreement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@generateagreement',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@generateagreement',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.generateagreement',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getMigrationAgentDetail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getMigrationAgentDetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getMigrationAgentDetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getMigrationAgentDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getMigrationAgentDetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getVisaAggreementMigrationAgentDetail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getVisaAggreementMigrationAgentDetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getVisaAggreementMigrationAgentDetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getVisaAggreementMigrationAgentDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getVisaAggreementMigrationAgentDetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getCostAssignmentMigrationAgentDetail' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getCostAssignmentMigrationAgentDetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getCostAssignmentMigrationAgentDetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getCostAssignmentMigrationAgentDetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getCostAssignmentMigrationAgentDetail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.savecostassignment' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/savecostassignment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@savecostassignment',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@savecostassignment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.savecostassignment',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::XipiKItHWxoQnisM' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/check-cost-assignment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkCostAssignment',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkCostAssignment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::XipiKItHWxoQnisM',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.savecostassignmentlead' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/savecostassignmentlead',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@savecostassignmentlead',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@savecostassignmentlead',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.savecostassignmentlead',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getCostAssignmentMigrationAgentDetailLead' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getCostAssignmentMigrationAgentDetailLead',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getCostAssignmentMigrationAgentDetailLead',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getCostAssignmentMigrationAgentDetailLead',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getCostAssignmentMigrationAgentDetailLead',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.uploadAgreement' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/{admin}/upload-agreement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@uploadAgreement',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@uploadAgreement',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.uploadAgreement',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'forms.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'forms',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Form956Controller@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\Form956Controller@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'forms.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'forms.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'forms/{form}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Form956Controller@show',
        'controller' => 'App\\Http\\Controllers\\CRM\\Form956Controller@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'forms.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'forms.preview' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'forms/{form}/preview',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Form956Controller@previewPdf',
        'controller' => 'App\\Http\\Controllers\\CRM\\Form956Controller@previewPdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'forms.preview',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'forms.pdf' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'forms/{form}/pdf',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Form956Controller@generatePdf',
        'controller' => 'App\\Http\\Controllers\\CRM\\Form956Controller@generatePdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'forms.pdf',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getmattertemplates' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-matter-templates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getmattertemplates',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@getmattertemplates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getmattertemplates',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getClientMatters' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-client-matters/{clientId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getClientMatters',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getClientMatters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getClientMatters',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::I1kNZGqlLzigGGuC' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/fetchClientMatterAssignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@fetchClientMatterAssignee',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@fetchClientMatterAssignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::I1kNZGqlLzigGGuC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WF1WGyM3AJLxV2FB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/updateClientMatterAssignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateClientMatterAssignee',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPersonalDetailsController@updateClientMatterAssignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::WF1WGyM3AJLxV2FB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'upload_checklists.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'upload-checklists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UploadChecklistController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\UploadChecklistController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'upload_checklists.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'upload_checklists.matter' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'upload-checklists/matter/{matterId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UploadChecklistController@showByMatter',
        'controller' => 'App\\Http\\Controllers\\CRM\\UploadChecklistController@showByMatter',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'upload_checklists.matter',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'upload_checklistsupload' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'upload-checklists/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\UploadChecklistController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\UploadChecklistController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'upload_checklistsupload',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::hdGVh4UbQCxn3MgW' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/personalfollowup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@personalfollowup',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@personalfollowup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::hdGVh4UbQCxn3MgW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::OrLcfm01aPA1cvoU' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/updatefollowup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@updatefollowup',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@updatefollowup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::OrLcfm01aPA1cvoU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::faaYFCZFEDQN7u5z' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/reassignfollowup/store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@reassignfollowupstore',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@reassignfollowupstore',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::faaYFCZFEDQN7u5z',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.updatesessioncompleted' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/update-session-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@updatesessioncompleted',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@updatesessioncompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.updatesessioncompleted',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.getAllUser' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/getAllUser',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@getAllUser',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@getAllUser',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.getAllUser',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.toggleClientPortal' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/toggle-client-portal',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPortalController@toggleClientPortal',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPortalController@toggleClientPortal',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.toggleClientPortal',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.approveAuditValue' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/client-portal-details/approve-audit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPortalController@approveAuditValue',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPortalController@approveAuditValue',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.approveAuditValue',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.rejectAuditValue' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'api/client-portal-details/reject-audit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientPortalController@rejectAuditValue',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientPortalController@rejectAuditValue',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.rejectAuditValue',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'anzsco.search' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'anzsco/search',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@search',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@search',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'anzsco.search',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'anzsco.getByCode' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'anzsco/code/{code}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@getByCode',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\AnzscoOccupationController@getByCode',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'anzsco.getByCode',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'check.email' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'check-email',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkEmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkEmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'check.email',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'check.phone' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'check.phone',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkContact',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkContact',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'check.phone',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::xseuxiBLDChhaJLX' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'save_tag',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@save_tag',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@save_tag',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::xseuxiBLDChhaJLX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'references.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'save-references',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@savereferences',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@savereferences',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'references.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'check.star.client' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'check-star-client',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkStarClient',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@checkStarClient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'check.star.client',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'client.merge_records' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'merge_records',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@merge_records',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@merge_records',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'client.merge_records',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'send-webhook' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'send-webhook',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ClientsController@sendToWebhook',
        'controller' => 'App\\Http\\Controllers\\CRM\\ClientsController@sendToWebhook',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'send-webhook',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::6TVRMKEfOVS0WD8w' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fetch-visa_expiry_messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchvisaexpirymessages',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchvisaexpirymessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::6TVRMKEfOVS0WD8w',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.email.verify' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'verify-email/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@verifyEmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\EmailVerificationController@verifyEmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.email.verify',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'applications.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'applications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'applications.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'applications.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'applications/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'applications.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'applications.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'applications/detail/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@detail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@detail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'applications.detail',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'applications.import' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'applications-import',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@import',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@import',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'applications.import',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GdJ5MbFWau7nzNAS' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getapplicationdetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationdetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::GdJ5MbFWau7nzNAS',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::lU2WXdJpOzCZKykK' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'load-application-insert-update-data',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@loadApplicationInsertUpdateData',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@loadApplicationInsertUpdateData',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::lU2WXdJpOzCZKykK',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ffd8Aiau1EZ7kbEl' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'updatestage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updatestage',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updatestage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::ffd8Aiau1EZ7kbEl',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::marmVqs8dVA0He56' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'completestage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@completestage',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@completestage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::marmVqs8dVA0He56',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::yDc6I2WUXjSjRNgD' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'updatebackstage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updatebackstage',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updatebackstage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::yDc6I2WUXjSjRNgD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::jCvCFKUh7Az7VukU' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-applications-logs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationslogs',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationslogs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::jCvCFKUh7Az7VukU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FAbqAB1rm0UnBWPW' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-applications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplications',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplications',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::FAbqAB1rm0UnBWPW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::eA4VHvzNSWTMivdV' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'discontinue_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@discontinue_application',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@discontinue_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::eA4VHvzNSWTMivdV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::rRw8WLGCxhuoNLsj' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'revert_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@revert_application',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@revert_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::rRw8WLGCxhuoNLsj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::hJwulnCcrxgrT93P' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'create-app-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@addNote',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@addNote',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::hJwulnCcrxgrT93P',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::oPndjGgHgkCgrORa' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getapplicationnotes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationnotes',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationnotes',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::oPndjGgHgkCgrORa',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4EUk4IO2wtfl24m3' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'application-sendmail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@applicationsendmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@applicationsendmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::4EUk4IO2wtfl24m3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.matter-messages' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/matter-messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getMatterMessages',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getMatterMessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.matter-messages',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.send-message' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'clients/send-message',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@sendMessageToClient',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@sendMessageToClient',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.send-message',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PSqflSYHpYhr6CWj' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/updateintake',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updateintake',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updateintake',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::PSqflSYHpYhr6CWj',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::x20b08M78zdp8B8X' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/updatedates',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updatedates',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updatedates',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::x20b08M78zdp8B8X',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PLfNqFPBdPcmruKP' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/updateexpectwin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updateexpectwin',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@updateexpectwin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::PLfNqFPBdPcmruKP',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Pn8ZzwBpBcXswqAJ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/getapplicationbycid',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationbycid',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@getapplicationbycid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Pn8ZzwBpBcXswqAJ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::NJJc0si4zSriE2ag' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'application/spagent_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@spagent_application',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@spagent_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::NJJc0si4zSriE2ag',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::YqQdxDakSAd5dGdr' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'application/sbagent_application',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@sbagent_application',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@sbagent_application',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::YqQdxDakSAd5dGdr',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::j5OQv8TnvH3wn3wZ' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'application/application_ownership',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@application_ownership',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@application_ownership',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::j5OQv8TnvH3wn3wZ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::pId8LXcx8PsvoTqq' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'superagent',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@superagent',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@superagent',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::pId8LXcx8PsvoTqq',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DiaOlBy798g0x5vW' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'subagent',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@subagent',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@subagent',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::DiaOlBy798g0x5vW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::SZJI3RjJt8MR59bO' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'applicationsavefee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@applicationsavefee',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@applicationsavefee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::SZJI3RjJt8MR59bO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::dzkb4qcL2d9kqOG8' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/export/pdf/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@exportapplicationpdf',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@exportapplicationpdf',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::dzkb4qcL2d9kqOG8',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Vmm5fqMzic4dFBRc' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'add-checklists',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@addchecklists',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@addchecklists',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Vmm5fqMzic4dFBRc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::SNXkVzlvGR4caO1T' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'application/checklistupload',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@checklistupload',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@checklistupload',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::SNXkVzlvGR4caO1T',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DL0m4y4JEideNlAU' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'deleteapplicationdocs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@deleteapplicationdocs',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@deleteapplicationdocs',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::DL0m4y4JEideNlAU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wHCI2qUQq6AhC7EL' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/publishdoc',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@publishdoc',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@publishdoc',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::wHCI2qUQq6AhC7EL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::rmoiTydaVAj3F9MH' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'application/approve-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@approveDocument',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@approveDocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::rmoiTydaVAj3F9MH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::yr4sN9phNckyrlrA' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'application/reject-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@rejectDocument',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@rejectDocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::yr4sN9phNckyrlrA',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::selsTB7zd78D2VgD' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'application/download-document',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@downloadDocument',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@downloadDocument',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::selsTB7zd78D2VgD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'migration.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'migration',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@migrationindex',
        'controller' => 'App\\Http\\Controllers\\CRM\\ApplicationsController@migrationindex',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'migration.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'officevisits.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'officevisits.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'officevisits.waiting' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits/waiting',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@waiting',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@waiting',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'officevisits.waiting',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'officevisits.attending' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits/attending',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@attending',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@attending',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'officevisits.attending',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'officevisits.completed' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits/completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@completed',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@completed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'officevisits.completed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'officevisits.archived' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits/archived',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@archived',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@archived',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'officevisits.archived',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'officevisits.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'officevisits.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EacnK5NdWZE0OY6U' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'checkin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@checkin',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@checkin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::EacnK5NdWZE0OY6U',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4CKMnrqiDe0hkKIc' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-checkin-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@getcheckin',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@getcheckin',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::4CKMnrqiDe0hkKIc',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::OBUrzvk26sopQNvz' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_visit_purpose',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@update_visit_purpose',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@update_visit_purpose',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::OBUrzvk26sopQNvz',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::M703Sivt9BaDmrCl' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_visit_comment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@update_visit_comment',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@update_visit_comment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::M703Sivt9BaDmrCl',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::kkHat1ifBW5ReJN0' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'attend_session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@attend_session',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@attend_session',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::kkHat1ifBW5ReJN0',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::3YJ5pyxvEBm85Axd' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'complete_session',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@complete_session',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@complete_session',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::3YJ5pyxvEBm85Axd',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::4PCyFUSHLrrl9vP4' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'office-visits/change_assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@change_assignee',
        'controller' => 'App\\Http\\Controllers\\CRM\\OfficeVisitController@change_assignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::4PCyFUSHLrrl9vP4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.index',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'appointments/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.create',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.store',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'appointments/{appointment}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.show',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@show',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'appointments/{appointment}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.edit',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'appointments/{appointment}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.update',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'appointments/{appointment}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'appointments.destroy',
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@destroy',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@destroy',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'appointments-adelaide' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'appointments-adelaide',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsAdelaide',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@appointmentsAdelaide',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'appointments-adelaide',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::bzakwN6RvzG4o72j' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'deleteappointment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@deleteappointment',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@deleteappointment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::bzakwN6RvzG4o72j',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WS1lAN3WxJysGXyd' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'add-appointment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@addAppointment',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@addAppointment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::WS1lAN3WxJysGXyd',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8VsoW497vTKBxwxi' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'add-appointment-book',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@addAppointmentBook',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@addAppointmentBook',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::8VsoW497vTKBxwxi',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Gc1twDezEwlKDUMr' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'editappointment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@editappointment',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@editappointment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Gc1twDezEwlKDUMr',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::iPfdD29xQYiGyXiC' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'updatefollowupschedule',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@updatefollowupschedule',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@updatefollowupschedule',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::iPfdD29xQYiGyXiC',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::mBoXKpHuDMgjgMLO' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'updateappointmentstatus/{status}/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@updateappointmentstatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@updateappointmentstatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::mBoXKpHuDMgjgMLO',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::v2xazoVNTj3mhBmX' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_appointment_status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_appointment_status',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_appointment_status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::v2xazoVNTj3mhBmX',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::xIhP1q1kEZrkUG8V' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_appointment_priority',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_appointment_priority',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_appointment_priority',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::xIhP1q1kEZrkUG8V',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::8LObr7s8skms99M8' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_apppointment_comment',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_apppointment_comment',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_apppointment_comment',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::8LObr7s8skms99M8',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::xJD89dftCdDeelTB' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update_apppointment_description',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_apppointment_description',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@update_apppointment_description',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::xJD89dftCdDeelTB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::cbOUff9IZarM0TSt' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@getAppointments',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@getAppointments',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::cbOUff9IZarM0TSt',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::DwytQf8PDrQMboCy' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'getAppointmentdetail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@getAppointmentdetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@getAppointmentdetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::DwytQf8PDrQMboCy',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::ou9pHVvMS1yae5lB' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'get-assigne-detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@assignedetail',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@assignedetail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::ou9pHVvMS1yae5lB',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::HJTb87W0DdcWfrS5' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'change_assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@change_assignee',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@change_assignee',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::HJTb87W0DdcWfrS5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'getdatetimebackend' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'getdatetimebackend',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@getDateTimeBackend',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@getDateTimeBackend',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'getdatetimebackend',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'getdisableddatetime' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'getdisableddatetime',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@getDisabledDateTime',
        'controller' => 'App\\Http\\Controllers\\CRM\\AppointmentsController@getDisabledDateTime',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'getdisableddatetime',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'booking/appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@index',
        'as' => 'booking.appointments.index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'booking/appointments/{id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@edit',
        'as' => 'booking.appointments.edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'booking/appointments/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@update',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@update',
        'as' => 'booking.appointments.update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'booking/appointments/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@show',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@show',
        'as' => 'booking.appointments.show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.json' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'booking/appointments/{id}/json',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@getAppointmentJson',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@getAppointmentJson',
        'as' => 'booking.appointments.json',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.calendar' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'booking/calendar/{type}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@calendar',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@calendar',
        'as' => 'booking.appointments.calendar',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'type' => 'paid|jrp|education|tourist|adelaide',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.update-status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'booking/appointments/{id}/update-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@updateStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@updateStatus',
        'as' => 'booking.appointments.update-status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.update-consultant' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'booking/appointments/{id}/update-consultant',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@updateConsultant',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@updateConsultant',
        'as' => 'booking.appointments.update-consultant',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.update-datetime' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'booking/appointments/{id}/update-datetime',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@update',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@update',
        'as' => 'booking.appointments.update-datetime',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.add-note' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'booking/appointments/{id}/add-note',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@addNote',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@addNote',
        'as' => 'booking.appointments.add-note',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.update-followup' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'booking/appointments/{id}/update-followup',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@updateFollowUp',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@updateFollowUp',
        'as' => 'booking.appointments.update-followup',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.send-reminder' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'booking/appointments/{id}/send-reminder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@sendReminder',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@sendReminder',
        'as' => 'booking.appointments.send-reminder',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.bulk-update-status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'booking/appointments/bulk-update-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@bulkUpdateStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@bulkUpdateStatus',
        'as' => 'booking.appointments.bulk-update-status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.appointments.export' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'booking/appointments/export',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@export',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@export',
        'as' => 'booking.appointments.export',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.sync.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'booking/sync/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@syncDashboard',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@syncDashboard',
        'as' => 'booking.sync.dashboard',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.sync.stats' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'booking/sync/stats',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@syncStats',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@syncStats',
        'as' => 'booking.sync.stats',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.sync.manual' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'booking/sync/manual',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
          2 => 'can:trigger-manual-sync',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@manualSync',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@manualSync',
        'as' => 'booking.sync.manual',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'booking.api.appointments' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'booking/api/appointments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@getAppointments',
        'controller' => 'App\\Http\\Controllers\\CRM\\BookingAppointmentsController@getAppointments',
        'as' => 'booking.api.appointments',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/booking',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'auditlogs.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'audit-logs',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AuditLogController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\AuditLogController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'auditlogs.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::Yq4KEjAoyqKOpTWE' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fetch-notification',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchnotification',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchnotification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::Yq4KEjAoyqKOpTWE',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::fWGMz7kaZn1Q4sBL' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fetch-messages',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchmessages',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchmessages',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::fWGMz7kaZn1Q4sBL',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::6TjzMn7Nzbx4vgmG' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fetch-office-visit-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchOfficeVisitNotifications',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchOfficeVisitNotifications',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::6TjzMn7Nzbx4vgmG',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::HRS1yBvgsr0rKsot' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'mark-notification-seen',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@markNotificationSeen',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@markNotificationSeen',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::HRS1yBvgsr0rKsot',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::otZiQqc5v97TgvjD' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'check-checkin-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@checkCheckinStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@checkCheckinStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::otZiQqc5v97TgvjD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::91bfcUOnUTMVXR0C' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-checkin-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateCheckinStatus',
        'controller' => 'App\\Http\\Controllers\\CRM\\DashboardController@updateCheckinStatus',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::91bfcUOnUTMVXR0C',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EeM5f9bLqthcIQg3' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'all-notifications',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@allnotification',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@allnotification',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::EeM5f9bLqthcIQg3',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::U58PkzaWCsdPzevT' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fetch-InPersonWaitingCount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchInPersonWaitingCount',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchInPersonWaitingCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::U58PkzaWCsdPzevT',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::44gjLdW68eFPMZGV' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'fetch-TotalActivityCount',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchTotalActivityCount',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@fetchTotalActivityCount',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::44gjLdW68eFPMZGV',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.index',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'assignee/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.create',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'assignee',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.store',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'assignee/{assignee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.show',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@show',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'assignee/{assignee}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.edit',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
        1 => 'PATCH',
      ),
      'uri' => 'assignee/{assignee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.update',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@update',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'assignee/{assignee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'as' => 'assignee.destroy',
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::5zMqKJ3UQLmKnSqY' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'assignee-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@completed',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@completed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::5zMqKJ3UQLmKnSqY',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::UTJlwDG240aw566B' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-task-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@updatetaskcompleted',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@updatetaskcompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::UTJlwDG240aw566B',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::wqkUU1XqbV6ZRwX4' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-task-not-completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@updatetasknotcompleted',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@updatetasknotcompleted',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::wqkUU1XqbV6ZRwX4',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.assigned_by_me' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'assigned_by_me',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@assigned_by_me',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@assigned_by_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'assignee.assigned_by_me',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.assigned_to_me' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'assigned_to_me',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@assigned_to_me',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@assigned_to_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'assignee.assigned_to_me',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy_by_me' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'destroy_by_me/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_by_me',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_by_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'assignee.destroy_by_me',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy_to_me' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'destroy_to_me/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_to_me',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_to_me',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'assignee.destroy_to_me',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.action_completed' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'action_completed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@action_completed',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@action_completed',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'assignee.action_completed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy_activity' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'destroy_activity/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_activity',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_activity',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'assignee.destroy_activity',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.destroy_complete_activity' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'destroy_complete_activity/{note_id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_complete_activity',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@destroy_complete_activity',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'assignee.destroy_complete_activity',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::9dYNvIE828M7r2WH' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'is_email_unique',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@is_email_unique',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@is_email_unique',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::9dYNvIE828M7r2WH',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::EouqD9PsPZDROHzG' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'is_contactno_unique',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@is_contactno_unique',
        'controller' => 'App\\Http\\Controllers\\CRM\\Leads\\LeadController@is_contactno_unique',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::EouqD9PsPZDROHzG',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::pxRenikyCePmmq7z' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'extenddeadlinedate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@extenddeadlinedate',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@extenddeadlinedate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::pxRenikyCePmmq7z',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::tASMWHOieFpmXZJh' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-stage',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@updateStage',
        'controller' => 'App\\Http\\Controllers\\CRM\\CRMUtilityController@updateStage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::tASMWHOieFpmXZJh',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::nbio4ftiz5TdQS3h' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'get_assignee_list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@get_assignee_list',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@get_assignee_list',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::nbio4ftiz5TdQS3h',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::PKdxSCzuhm0APXvk' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'update-task',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@updateTask',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@updateTask',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::PKdxSCzuhm0APXvk',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'action.counts' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'action/counts',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@getActionCounts',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@getActionCounts',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'action.counts',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'assignee.action' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'action',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@action',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@action',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'assignee.action',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'action.list' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'action/list',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\AssigneeController@getAction',
        'controller' => 'App\\Http\\Controllers\\CRM\\AssigneeController@getAction',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'action.list',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'test.signature' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-signature',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:53:"function () {
    return \\view(\'test-signature\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000ac70000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'test.signature',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'doc-to-pdf.form' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'doc-to-pdf',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@showForm',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@showForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'doc-to-pdf.form',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'doc-to-pdf.convert' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'doc-to-pdf/convert',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@convertLocal',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@convertLocal',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'doc-to-pdf.convert',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'doc-to-pdf.test' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'doc-to-pdf/test',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@testLocalConversion',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@testLocalConversion',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'doc-to-pdf.test',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'doc-to-pdf.test-python' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'doc-to-pdf/test-python',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@testPythonConversion',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@testPythonConversion',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'doc-to-pdf.test-python',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'doc-to-pdf.debug' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'doc-to-pdf/debug',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@debugConfig',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocToPdfController@debugConfig',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'doc-to-pdf.debug',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'signatures',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@index',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@index',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'signatures/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.suggest-association' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/suggest-association',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@suggestAssociation',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@suggestAssociation',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.suggest-association',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.preview-email' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/preview-email',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@previewEmail',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@previewEmail',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.preview-email',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.bulk-archive' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/bulk-archive',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@bulkArchive',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@bulkArchive',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.bulk-archive',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.bulk-void' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/bulk-void',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@bulkVoid',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@bulkVoid',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.bulk-void',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.bulk-resend' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/bulk-resend',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@bulkResend',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@bulkResend',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.bulk-resend',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'signatures/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@show',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@show',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.reminder' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/{id}/reminder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@sendReminder',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@sendReminder',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.reminder',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.send' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/{id}/send',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@sendForSignature',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@sendForSignature',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.send',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.copy-link' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'signatures/{id}/copy-link',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@copyLink',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@copyLink',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.copy-link',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.associate' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/{id}/associate',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@associate',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@associate',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.associate',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.client-matters' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'signatures/api/client-matters/{clientId}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@getClientMatters',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@getClientMatters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.client-matters',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'signatures.detach' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'signatures/{id}/detach',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@detach',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@detach',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/signatures',
        'where' => 
        array (
        ),
        'as' => 'signatures.detach',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'clients.matters' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'clients/{id}/matters',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@getClientMatters',
        'controller' => 'App\\Http\\Controllers\\CRM\\SignatureDashboardController@getClientMatters',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'clients.matters',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'debug.pdf.page' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'debug-pdf-page/{id}/{page}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:3851:"function($id, $page) {
    // Clear any output buffers to prevent corruption
    if (\\ob_get_level()) {
        \\ob_end_clean();
    }
    
    try {
        $document = \\App\\Models\\Document::findOrFail($id);
        $url = $document->myfile;
        $tmpPdfPath = null;
        $isLocalFile = false;
        
        // Check if URL is a full S3 URL or local path
        if ($url && \\filter_var($url, FILTER_VALIDATE_URL) && \\strpos($url, \'s3\') !== false) {
            // This is an S3 URL - extract the key
            $s3Key = null;
            $parsed = \\parse_url($url);
            if (isset($parsed[\'path\'])) {
                $s3Key = \\ltrim(\\urldecode($parsed[\'path\']), \'/\');
            }
            
            if ($s3Key && \\Storage::disk(\'s3\')->exists($s3Key)) {
                // Download PDF from S3 to a temp file
                $tmpPdfPath = \\storage_path(\'app/tmp_\' . \\uniqid() . \'.pdf\');
                $pdfStream = \\Storage::disk(\'s3\')->get($s3Key);
                \\file_put_contents($tmpPdfPath, $pdfStream);
                \\Log::info(\'Debug route: Downloaded S3 file for preview\', [\'s3Key\' => $s3Key, \'tempPath\' => $tmpPdfPath]);
            }
        } elseif ($url && \\file_exists(\\storage_path(\'app/public/\' . $url))) {
            // This is a local file path and file exists
            $tmpPdfPath = \\storage_path(\'app/public/\' . $url);
            $isLocalFile = true;
        } else {
            // Try to build S3 key from DB fields as fallback
            if (!empty($document->myfile_key) && !empty($document->doc_type) && !empty($document->client_id)) {
                $s3Key = $document->client_id . \'/\' . $document->doc_type . \'/\' . $document->myfile_key;
                
                if (\\Storage::disk(\'s3\')->exists($s3Key)) {
                    // Download PDF from S3 to a temp file
                    $tmpPdfPath = \\storage_path(\'app/tmp_\' . \\uniqid() . \'.pdf\');
                    $pdfStream = \\Storage::disk(\'s3\')->get($s3Key);
                    \\file_put_contents($tmpPdfPath, $pdfStream);
                    \\Log::info(\'Debug route: Downloaded S3 file via fallback\', [\'s3Key\' => $s3Key, \'tempPath\' => $tmpPdfPath]);
                }
            }
        }
        
        if ($tmpPdfPath && \\file_exists($tmpPdfPath)) {
            $pdfService = \\app(\\App\\Services\\PythonPDFService::class);
            if ($pdfService->isHealthy()) {
                $result = $pdfService->convertPageToImage($tmpPdfPath, $page, 150);
                
                // Clean up temp file (only if it was created from S3, not local file)
                if (!$isLocalFile) {
                    @\\unlink($tmpPdfPath);
                }
                
                if ($result && ($result[\'success\'] ?? false)) {
                    $imageData = \\base64_decode(\\explode(\',\', $result[\'image_data\'])[1]);
                    
                    // Return raw binary response with proper headers
                    return \\response($imageData, 200, [
                        \'Content-Type\' => \'image/png\',
                        \'Content-Length\' => \\strlen($imageData),
                        \'Cache-Control\' => \'public, max-age=3600\',
                    ]);
                }
            }
            
            // Clean up on failure
            if (!$isLocalFile && \\file_exists($tmpPdfPath)) {
                @\\unlink($tmpPdfPath);
            }
        }
        
        return \\response()->json([\'error\' => \'Failed to generate image\', \'document_id\' => $id, \'page\' => $page], 500);
    } catch (\\Exception $e) {
        \\Log::error(\'Debug route error\', [\'error\' => $e->getMessage(), \'document_id\' => $id, \'page\' => $page]);
        return \\response()->json([\'error\' => $e->getMessage()], 500);
    }
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000c710000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'debug.pdf.page',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'public.documents.sign' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'sign/{id}/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\PublicDocumentController@sign',
        'controller' => 'App\\Http\\Controllers\\PublicDocumentController@sign',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'public.documents.sign',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'public.documents.submitSignatures' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/{document}/sign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\PublicDocumentController@submitSignatures',
        'controller' => 'App\\Http\\Controllers\\PublicDocumentController@submitSignatures',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'public.documents.submitSignatures',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'public.documents.page' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/{id}/page/{page}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\PublicDocumentController@getPage',
        'controller' => 'App\\Http\\Controllers\\PublicDocumentController@getPage',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'public.documents.page',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/{id?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:160:"function($id = null) {
    if ($id) {
        return \\redirect()->route(\'signatures.show\', $id);
    }
    return \\redirect()->route(\'signatures.index\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000c950000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.index',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'id' => '[0-9]+',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.download.signed' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/{id}/download-signed',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@downloadSigned',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@downloadSigned',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.download.signed',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.download_and_thankyou' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/{id}/download-signed-and-thankyou',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@downloadSignedAndThankyou',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@downloadSignedAndThankyou',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.download_and_thankyou',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'public.documents.thankyou' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/thankyou/{id?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\PublicDocumentController@thankyou',
        'controller' => 'App\\Http\\Controllers\\PublicDocumentController@thankyou',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'public.documents.thankyou',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.sendReminder' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/{document}/send-reminder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@sendReminder',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@sendReminder',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.sendReminder',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@create',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@create',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.create',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@store',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@store',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/{id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@edit',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@edit',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.edit',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.update' => 
    array (
      'methods' => 
      array (
        0 => 'PATCH',
      ),
      'uri' => 'documents/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@update',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@update',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.sendSigningLink' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'documents/{document}/send-signing-link',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@sendSigningLink',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@sendSigningLink',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.sendSigningLink',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'documents.showSignForm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'documents/{document}/sign',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => 'App\\Http\\Controllers\\CRM\\DocumentController@showSignForm',
        'controller' => 'App\\Http\\Controllers\\CRM\\DocumentController@showSignForm',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'documents.showSignForm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'webhooks.sms.twilio.status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'webhooks/sms/twilio/status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@twilioStatus',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@twilioStatus',
        'as' => 'webhooks.sms.twilio.status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/webhooks/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'webhooks.sms.twilio.incoming' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'webhooks/sms/twilio/incoming',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@twilioIncoming',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@twilioIncoming',
        'as' => 'webhooks.sms.twilio.incoming',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/webhooks/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'webhooks.sms.cellcast.status' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'webhooks/sms/cellcast/status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@cellcastStatus',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@cellcastStatus',
        'as' => 'webhooks.sms.cellcast.status',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/webhooks/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'webhooks.sms.cellcast.incoming' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'webhooks/sms/cellcast/incoming',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@cellcastIncoming',
        'controller' => 'App\\Http\\Controllers\\AdminConsole\\Sms\\SmsWebhookController@cellcastIncoming',
        'as' => 'webhooks.sms.cellcast.incoming',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '/webhooks/sms',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7GVklrR9h8QuUo6X' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-messaging',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:336:"function () {
    // Create a test user session for development
    if (!\\auth()->check()) {
        // Use the Super1 Admin1 account for testing
        $admin = \\App\\Models\\Admin::find(1); // Super1 Admin1
        if ($admin) {
            \\auth()->login($admin);
        }
    }
    return \\view(\'messaging.integration\');
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000c8b0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::7GVklrR9h8QuUo6X',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::WL9gfFozjy4koSfW' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-broadcast',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:906:"function () {
    // Create a test user session for development
    if (!\\auth()->check()) {
        $admin = \\App\\Models\\Admin::find(1); // Super1 Admin1
        if ($admin) {
            \\auth()->login($admin);
        }
    }
    
    // Send a test broadcast
    \\broadcast(new \\App\\Events\\MessageSent([
        \'id\' => 999,
        \'subject\' => \'Test Broadcast Message\',
        \'message\' => \'This is a test broadcast message to verify real-time functionality.\',
        \'sender\' => \'System\',
        \'recipient\' => \\auth()->user()->first_name . \' \' . \\auth()->user()->last_name,
        \'message_type\' => \'normal\',
        \'sent_at\' => \\now()->toISOString(),
        \'is_read\' => false
    ], \\auth()->id()));
    
    return \\response()->json([
        \'success\' => true,
        \'message\' => \'Test broadcast sent successfully\',
        \'user_id\' => \\auth()->id()
    ]);
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000cab0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::WL9gfFozjy4koSfW',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::GtzEf9K9QYpzoaTm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-delete-message/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:1230:"function ($id) {
    // Create a test user session for development
    if (!\\auth()->check()) {
        $admin = \\App\\Models\\Admin::find(1); // Super1 Admin1
        if ($admin) {
            \\auth()->login($admin);
        }
    }
    
    try {
        // Delete the message
        $deleted = \\DB::table(\'messages\')
            ->where(\'id\', $id)
            ->where(\'recipient_id\', \\auth()->id()) // Only allow deleting messages received by the current user
            ->delete();
        
        if ($deleted) {
            // Broadcast the deletion
            \\broadcast(new \\App\\Events\\MessageDeleted($id, \\auth()->id()));
            
            return \\response()->json([
                \'success\' => true,
                \'message\' => "Message {$id} deleted successfully"
            ]);
        } else {
            return \\response()->json([
                \'success\' => false,
                \'message\' => \'Message not found or not authorized to delete\'
            ], 404);
        }
    } catch (\\Exception $e) {
        return \\response()->json([
            \'success\' => false,
            \'message\' => \'Error deleting message: \' . $e->getMessage()
        ], 500);
    }
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000cad0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::GtzEf9K9QYpzoaTm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::c0AEwAEXB6d3WFP5' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-create-message',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:2271:"function () {
    // Create a test user session for development
    if (!\\auth()->check()) {
        $admin = \\App\\Models\\Admin::find(1); // Super1 Admin1
        if ($admin) {
            \\auth()->login($admin);
        }
    }
    
    try {
        // Create a new test message
        $messageId = \\DB::table(\'messages\')->insertGetId([
            \'subject\' => \'Test Message \' . \\time(),
            \'message\' => \'This is a test message created at \' . \\now()->toDateTimeString(),
            \'sender\' => \'System Test\',
            \'recipient\' => \\auth()->user()->first_name . \' \' . \\auth()->user()->last_name,
            \'sender_id\' => 36464, // Vipul Kumar
            \'recipient_id\' => \\auth()->id(),
            \'sent_at\' => \\now(),
            \'is_read\' => false,
            \'message_type\' => \'normal\',
            \'client_matter_id\' => 9,
            \'client_matter_stage_id\' => 1,
            \'created_at\' => \\now(),
            \'updated_at\' => \\now()
        ]);
        
        // Broadcast the new message
        \\broadcast(new \\App\\Events\\MessageSent([
            \'id\' => $messageId,
            \'subject\' => \'Test Message \' . \\time(),
            \'message\' => \'This is a test message created at \' . \\now()->toDateTimeString(),
            \'sender\' => \'System Test\',
            \'recipient\' => \\auth()->user()->first_name . \' \' . \\auth()->user()->last_name,
            \'message_type\' => \'normal\',
            \'sent_at\' => \\now()->toISOString(),
            \'is_read\' => false
        ], \\auth()->id()));
        
        // Broadcast unread count update
        $unreadCount = \\DB::table(\'messages\')
            ->where(\'recipient_id\', \\auth()->id())
            ->where(\'is_read\', false)
            ->count();
        \\broadcast(new \\App\\Events\\UnreadCountUpdated(\\auth()->id(), $unreadCount));
        
        return \\response()->json([
            \'success\' => true,
            \'message\' => "Test message {$messageId} created successfully",
            \'unread_count\' => $unreadCount
        ]);
    } catch (\\Exception $e) {
        return \\response()->json([
            \'success\' => false,
            \'message\' => \'Error creating message: \' . $e->getMessage()
        ], 500);
    }
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000caf0000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::c0AEwAEXB6d3WFP5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::lkGU6cDtylzrvQ5A' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'test-mark-read/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:2633:"function ($id) {
    // Create a test user session for development
    if (!\\auth()->check()) {
        $admin = \\App\\Models\\Admin::find(1); // Super1 Admin1
        if ($admin) {
            \\auth()->login($admin);
        }
    }
    
    try {
        // Get the message
        $message = \\DB::table(\'messages\')
            ->where(\'id\', $id)
            ->where(\'recipient_id\', \\auth()->id())
            ->first();
        
        if (!$message) {
            return \\response()->json([
                \'success\' => false,
                \'message\' => \'Message not found\'
            ], 404);
        }
        
        if (!$message->is_read) {
            // Mark as read
            \\DB::table(\'messages\')
                ->where(\'id\', $id)
                ->update([
                    \'is_read\' => true,
                    \'read_at\' => \\now(),
                    \'updated_at\' => \\now()
                ]);
            
            // Get updated message
            $updatedMessage = \\DB::table(\'messages\')->where(\'id\', $id)->first();
            
            // Broadcast the update
            \\broadcast(new \\App\\Events\\MessageUpdated([
                \'id\' => $updatedMessage->id,
                \'subject\' => $updatedMessage->subject,
                \'message\' => $updatedMessage->message,
                \'sender\' => $updatedMessage->sender,
                \'recipient\' => $updatedMessage->recipient,
                \'is_read\' => true,
                \'read_at\' => $updatedMessage->read_at,
                \'message_type\' => $updatedMessage->message_type,
                \'sent_at\' => $updatedMessage->sent_at
            ], \\auth()->id()));
            
            // Broadcast unread count update
            $unreadCount = \\DB::table(\'messages\')
                ->where(\'recipient_id\', \\auth()->id())
                ->where(\'is_read\', false)
                ->count();
            \\broadcast(new \\App\\Events\\UnreadCountUpdated(\\auth()->id(), $unreadCount));
            
            return \\response()->json([
                \'success\' => true,
                \'message\' => "Message {$id} marked as read",
                \'unread_count\' => $unreadCount
            ]);
        } else {
            return \\response()->json([
                \'success\' => false,
                \'message\' => \'Message is already read\'
            ]);
        }
    } catch (\\Exception $e) {
        return \\response()->json([
            \'success\' => false,
            \'message\' => \'Error updating message: \' . $e->getMessage()
        ], 500);
    }
}";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000cb10000000000000000";}}',
        'namespace' => 'App\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::lkGU6cDtylzrvQ5A',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::VqVSL1L5EzncAKUn' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'POST',
        2 => 'HEAD',
      ),
      'uri' => 'broadcasting/auth',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:admin',
        ),
        'uses' => '\\Illuminate\\Broadcasting\\BroadcastController@authenticate',
        'controller' => '\\Illuminate\\Broadcasting\\BroadcastController@authenticate',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'excluded_middleware' => 
        array (
          0 => 'Illuminate\\Foundation\\Http\\Middleware\\VerifyCsrfToken',
        ),
        'as' => 'generated::VqVSL1L5EzncAKUn',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
