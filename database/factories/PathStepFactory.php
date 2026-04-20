<?php

namespace Database\Factories;

use App\Models\Path;
use App\Models\PathStep;
use Illuminate\Database\Eloquent\Factories\Factory;

class PathStepFactory extends Factory
{
    protected $model = PathStep::class;

    private static array $steps = [
        [
            'title' => 'PHP & OOP Foundations',
            'type' => 'reading',
            'description' => 'Master the building blocks of server-side PHP development with a strong focus on Object-Oriented Programming. You will learn how PHP executes on the server, how to structure code using classes and interfaces, and why encapsulation and inheritance lead to more maintainable applications. This step sets the foundation for every Laravel concept you will encounter later.',
            'instructions' => [
                ['id' => 1, 'text' => 'Understand PHP\'s request lifecycle and how it differs from client-side scripting'],
                ['id' => 2, 'text' => 'Define classes, properties, and methods using visibility modifiers'],
                ['id' => 3, 'text' => 'Apply inheritance and interfaces to model real-world relationships'],
                ['id' => 4, 'text' => 'Use namespaces and PSR-4 autoloading with Composer'],
                ['id' => 5, 'text' => 'Write clean, type-hinted PHP 8.x code with union types and match expressions'],
            ],
            'resources' => [
                ['label' => 'PHP Manual — OOP', 'url' => 'https://www.php.net/manual/en/language.oop5.php'],
                ['label' => 'PHP 8 What\'s New', 'url' => 'https://stitcher.io/blog/new-in-php-8'],
            ],
        ],
        [
            'title' => 'Laravel Essentials',
            'type' => 'reading',
            'description' => 'Get hands-on with the most popular PHP framework in the industry. Laravel abstracts common web development tasks — routing, database access, authentication, and validation — so you can focus on business logic. By the end of this step you will be able to scaffold a working web application from scratch using Artisan, Eloquent, and Blade.',
            'instructions' => [
                ['id' => 1, 'text' => 'Set up a new Laravel project and understand the directory structure'],
                ['id' => 2, 'text' => 'Define routes, controllers, and return JSON or Blade responses'],
                ['id' => 3, 'text' => 'Use Eloquent models to perform CRUD operations without raw SQL'],
                ['id' => 4, 'text' => 'Validate incoming requests using Form Request classes'],
                ['id' => 5, 'text' => 'Run database migrations and seeders to manage schema changes'],
            ],
            'resources' => [
                ['label' => 'Laravel Docs', 'url' => 'https://laravel.com/docs'],
                ['label' => 'Laracasts — Laravel From Scratch', 'url' => 'https://laracasts.com/series/laravel-8-from-scratch'],
            ],
        ],
        [
            'title' => 'MySQL & Database Design',
            'type' => 'reading',
            'description' => 'A well-designed database is the backbone of every production application. This step covers the relational model in depth — from normalisation and schema design to indexing strategies and query optimisation. You will learn how to think like a DBA and write efficient queries that scale as your data grows.',
            'instructions' => [
                ['id' => 1, 'text' => 'Design normalised schemas up to 3NF and know when to denormalise'],
                ['id' => 2, 'text' => 'Write complex JOINs, subqueries, and aggregate functions'],
                ['id' => 3, 'text' => 'Create and choose the right indexes (B-Tree, composite, covering)'],
                ['id' => 4, 'text' => 'Use EXPLAIN to analyse query execution plans'],
                ['id' => 5, 'text' => 'Understand transactions, ACID guarantees, and locking behaviour'],
            ],
            'resources' => [
                ['label' => 'Use The Index, Luke', 'url' => 'https://use-the-index-luke.com'],
                ['label' => 'MySQL 8 Reference Manual', 'url' => 'https://dev.mysql.com/doc/refman/8.0/en/'],
            ],
        ],
        [
            'title' => 'RESTful API Development',
            'type' => 'lab',
            'description' => 'REST is the dominant architectural style for APIs consumed by mobile apps, SPAs, and third-party integrations. In this lab you will build a fully functional REST API with Laravel, covering resource routing, API Resources for response shaping, token-based authentication with Sanctum, and proper HTTP status codes. You will leave with a production-ready API skeleton.',
            'instructions' => [
                ['id' => 1, 'text' => 'Implement CRUD endpoints following REST conventions (GET, POST, PUT, DELETE)'],
                ['id' => 2, 'text' => 'Transform model data with Laravel API Resources and Resource Collections'],
                ['id' => 3, 'text' => 'Secure endpoints with Laravel Sanctum token authentication'],
                ['id' => 4, 'text' => 'Handle validation errors and return RFC 7807 problem responses'],
                ['id' => 5, 'text' => 'Write feature tests using Laravel\'s HTTP testing helpers'],
            ],
            'challenge_prompt' => 'Build a fully authenticated REST API for a Task Manager: users can register, log in, and manage their own tasks (create, list, update, delete). Protect all task routes with Sanctum. Include at least one test per endpoint.',
            'resources' => [
                ['label' => 'Laravel API Resources', 'url' => 'https://laravel.com/docs/eloquent-resources'],
                ['label' => 'REST API Design Best Practices', 'url' => 'https://restfulapi.net'],
            ],
        ],
        [
            'title' => 'Git & Version Control',
            'type' => 'reading',
            'description' => 'Version control is a non-negotiable skill for every professional developer. This step goes beyond basic commits to cover the branching strategies and collaboration workflows used by engineering teams at scale — including Git Flow, trunk-based development, and pull request best practices. You will also learn how to recover from common mistakes confidently.',
            'instructions' => [
                ['id' => 1, 'text' => 'Understand the Git object model: blobs, trees, commits, and refs'],
                ['id' => 2, 'text' => 'Branch, merge, and rebase with a clear mental model of what each does'],
                ['id' => 3, 'text' => 'Write atomic, conventional commits and meaningful PR descriptions'],
                ['id' => 4, 'text' => 'Resolve merge conflicts and use git bisect to find regressions'],
                ['id' => 5, 'text' => 'Apply Git Flow or trunk-based development in a team context'],
            ],
            'resources' => [
                ['label' => 'Pro Git Book (free)', 'url' => 'https://git-scm.com/book/en/v2'],
                ['label' => 'Conventional Commits', 'url' => 'https://www.conventionalcommits.org'],
            ],
        ],
        [
            'title' => 'Docker & Local Dev Environment',
            'type' => 'lab',
            'description' => 'Containers have revolutionised how software is developed, shipped, and run. This lab walks you through containerising a Laravel + MySQL + Nginx stack with Docker Compose, mirroring how production environments are configured. You will understand image layering, volume mounts, networking, and how to keep your development environment reproducible across any machine.',
            'instructions' => [
                ['id' => 1, 'text' => 'Write a Dockerfile for a PHP-FPM Laravel application'],
                ['id' => 2, 'text' => 'Compose multi-service environments (app, db, redis, nginx) with Docker Compose'],
                ['id' => 3, 'text' => 'Manage persistent data with named volumes and bind mounts'],
                ['id' => 4, 'text' => 'Configure service networking and environment variable injection'],
                ['id' => 5, 'text' => 'Use multi-stage builds to create lean production images'],
            ],
            'resources' => [
                ['label' => 'Docker Official Docs', 'url' => 'https://docs.docker.com'],
                ['label' => 'Docker Compose Docs', 'url' => 'https://docs.docker.com/compose/'],
            ],
        ],
        [
            'title' => 'AWS Basics for Developers',
            'type' => 'reading',
            'description' => 'Cloud infrastructure is a core competency for backend engineers working in Ireland\'s tech sector. This step introduces the AWS services you will encounter most often on the job: EC2, S3, RDS, and IAM. You will understand the shared responsibility model, how to deploy a basic application, and how to manage permissions securely — essential knowledge for any technical interview.',
            'instructions' => [
                ['id' => 1, 'text' => 'Navigate the AWS console and understand regions, AZs, and VPCs'],
                ['id' => 2, 'text' => 'Launch and connect to an EC2 instance; understand security groups'],
                ['id' => 3, 'text' => 'Store and retrieve objects from S3 with appropriate bucket policies'],
                ['id' => 4, 'text' => 'Provision a managed RDS instance and connect from your application'],
                ['id' => 5, 'text' => 'Create IAM users, roles, and policies following least-privilege principles'],
            ],
            'resources' => [
                ['label' => 'AWS Free Tier', 'url' => 'https://aws.amazon.com/free/'],
                ['label' => 'AWS Well-Architected Framework', 'url' => 'https://aws.amazon.com/architecture/well-architected/'],
            ],
        ],
        [
            'title' => 'CV & LinkedIn Optimisation',
            'type' => 'challenge',
            'description' => 'Your CV and LinkedIn profile are your professional shop window in the Irish and EU tech job market. Recruiters spend an average of 7 seconds scanning a CV — this step teaches you how to pass the ATS filter, tell a compelling career story, and position yourself for the roles you actually want. You will also learn how to leverage LinkedIn\'s algorithm to get inbound interest from recruiters.',
            'instructions' => [
                ['id' => 1, 'text' => 'Structure your CV for ATS compatibility (clean formatting, keyword alignment)'],
                ['id' => 2, 'text' => 'Write achievement-oriented bullet points using the CAR framework (Context, Action, Result)'],
                ['id' => 3, 'text' => 'Optimise your LinkedIn headline, About section, and skills for search visibility'],
                ['id' => 4, 'text' => 'Build a consistent personal brand across GitHub, LinkedIn, and your CV'],
                ['id' => 5, 'text' => 'Tailor your CV for each job application without starting from scratch'],
            ],
            'challenge_prompt' => 'Rewrite your CV using the CAR framework for every bullet point. Then update your LinkedIn About section to include your target role, your top 3 technical skills, and one concrete project outcome. Share both with your consultant for feedback.',
            'resources' => [
                ['label' => 'Tech CV Template', 'url' => 'https://www.overleaf.com/gallery/tagged/cv'],
                ['label' => 'LinkedIn Profile Checklist', 'url' => 'https://business.linkedin.com/talent-solutions/resources/talent-acquisition/linkedin-profile-completeness'],
            ],
        ],
    ];

    public function definition(): array
    {
        $step = fake()->randomElement(self::$steps);
        $order = fake()->numberBetween(0, 20);

        return [
            'path_id' => Path::factory(),
            'title' => $step['title'],
            'description' => $step['description'],
            'type' => $step['type'],
            'order' => $order,
            'instructions' => $step['instructions'] ?? null,
            'challenge_prompt' => $step['challenge_prompt'] ?? null,
            'resources' => $step['resources'] ?? null,
            'course_id' => null,
        ];
    }
}
