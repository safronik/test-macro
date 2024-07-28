<?php

namespace Controllers\Cli;

use Entities\Article;
use Entities\Comment;
use Entities\User;
use Safronik\DB\DB;
use Safronik\DBMigrator\DBMigrator;
use Safronik\Gateways\DBMigratorGateway;
use Safronik\Models\SchemaProviders\DomainsSchemaProvider;
use Safronik\Repositories\Repository;

final class MigrationsController extends CliController{
    
    private DBMigrator $migrator;
    
    /**
     * Creates migrator instance
     *
     * @return void
     */
    protected function init(): void
    {
        $this->migrator = new DBMigrator(
            new DBMigratorGateway(
                DB::getInstance()
            )
        );
    }
    
    public function methodInitDB()
    {
        try{
            $domains_schema_provider = new DomainsSchemaProvider(
                __DIR__ . '/../../Entities',
                'Entities'
            );
            
            $this->migrator
                ->setSchemas( $domains_schema_provider->getDomainsSchemas() )
                ->compareWithCurrentStructure()
                ->actualizeSchema();
            
        }catch( \Exception $exception){
            echo $exception->getMessage() . "\n";
        }
    }
    
    public function methodAddTestData()
    {
        try{
            
            $user_repo = new Repository( DB::getInstance(), User::class );
            $users = User::fabric( [
                [ 1, 'first_login',  'Ivan',  'Ivanov',  'ivanov@mail.ru' ],
                [ 2, 'second_login', 'Petr',  'Ptervov', 'petrov@mail.ru' ],
                [ 3, 'third_login',  'Sidor', 'Sidorov', 'sidorov@mail.ru' ],
            ]);
            foreach($users as $user){
                $user_repo->save( $user );
            }
            
            $article_repo = new Repository( DB::getInstance(), Article::class );
            $articles = Article::fabric( [
                [ 1, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',  'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Est mollit congue eros anim fugiat vero excepteur eu. Nostrud cillum quod elitr wisi iusto quis proident gubergren ad non rebum. Mazim aliquam tincidunt eu nostrud invidunt suscipit. Eum clita enim. Eirmod accumsan voluptua. Eirmod cillum exercitation.',  ],
                [ 2, 2, 'Lorem ipsum dolor sit amet',                                'Consetetur accumsan, nobis aliquip dolore fugiat dolores nibh aliqua vel autem, takimata ut reprehenderit justo iure eros commodo suscipit quod minim option minim cillum nonumy. Nibh mazim euismod vel feugiat nisl feugait dolores justo cupiditat in nibh eos. Magna justo consequat veniam, accusam sunt aliqua accumsan tincidunt aliqua congue rebum aliquip adipiscing sed justo tempor sint aute eleifend iriure. Nisl ipsum dignissim.', ],
                [ 3, 2, 'Lorem ipsum',                                               'Sea volutpat et voluptua te obcaecat gubergren te iure aliquip ex proident deserunt nibh sadipscing, laoreet eros ullamcorper, officia accusam vel possim id laoreet gubergren officia placerat consectetur quis incidunt praesent gubergren stet mazim eros. Accumsan illum elit cillum, adipisici iusto kasd exercitation nisi sunt illum te ut aliquyam vel imperdiet. Option laborum ea nonummy stet vulputate iriure ipsum id dolor labore lorem takimata, consectetur volutpat nam placerat augue sed accusam eu takimata odio dignissim deserunt illum te laboris reprehenderit rebum nostrud. At ad iusto esse nihil feugait iriure aliquam nisi commodo quis augue dolore. Fugiat eum laborum.', ],
                [ 4, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',  'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Est mollit congue eros anim fugiat vero excepteur eu. Nostrud cillum quod elitr wisi iusto quis proident gubergren ad non rebum. Mazim aliquam tincidunt eu nostrud invidunt suscipit. Eum clita enim. Eirmod accumsan voluptua. Eirmod cillum exercitation.',  ],
                [ 5, 2, 'Lorem ipsum dolor sit amet',                                'Consetetur accumsan, nobis aliquip dolore fugiat dolores nibh aliqua vel autem, takimata ut reprehenderit justo iure eros commodo suscipit quod minim option minim cillum nonumy. Nibh mazim euismod vel feugiat nisl feugait dolores justo cupiditat in nibh eos. Magna justo consequat veniam, accusam sunt aliqua accumsan tincidunt aliqua congue rebum aliquip adipiscing sed justo tempor sint aute eleifend iriure. Nisl ipsum dignissim.', ],
                [ 6, 2, 'Lorem ipsum',                                               'Sea volutpat et voluptua te obcaecat gubergren te iure aliquip ex proident deserunt nibh sadipscing, laoreet eros ullamcorper, officia accusam vel possim id laoreet gubergren officia placerat consectetur quis incidunt praesent gubergren stet mazim eros. Accumsan illum elit cillum, adipisici iusto kasd exercitation nisi sunt illum te ut aliquyam vel imperdiet. Option laborum ea nonummy stet vulputate iriure ipsum id dolor labore lorem takimata, consectetur volutpat nam placerat augue sed accusam eu takimata odio dignissim deserunt illum te laboris reprehenderit rebum nostrud. At ad iusto esse nihil feugait iriure aliquam nisi commodo quis augue dolore. Fugiat eum laborum.', ],
                [ 7, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',  'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Est mollit congue eros anim fugiat vero excepteur eu. Nostrud cillum quod elitr wisi iusto quis proident gubergren ad non rebum. Mazim aliquam tincidunt eu nostrud invidunt suscipit. Eum clita enim. Eirmod accumsan voluptua. Eirmod cillum exercitation.',  ],
                [ 8, 2, 'Lorem ipsum dolor sit amet',                                'Consetetur accumsan, nobis aliquip dolore fugiat dolores nibh aliqua vel autem, takimata ut reprehenderit justo iure eros commodo suscipit quod minim option minim cillum nonumy. Nibh mazim euismod vel feugiat nisl feugait dolores justo cupiditat in nibh eos. Magna justo consequat veniam, accusam sunt aliqua accumsan tincidunt aliqua congue rebum aliquip adipiscing sed justo tempor sint aute eleifend iriure. Nisl ipsum dignissim.', ],
                [ 9, 2, 'Lorem ipsum',                                               'Sea volutpat et voluptua te obcaecat gubergren te iure aliquip ex proident deserunt nibh sadipscing, laoreet eros ullamcorper, officia accusam vel possim id laoreet gubergren officia placerat consectetur quis incidunt praesent gubergren stet mazim eros. Accumsan illum elit cillum, adipisici iusto kasd exercitation nisi sunt illum te ut aliquyam vel imperdiet. Option laborum ea nonummy stet vulputate iriure ipsum id dolor labore lorem takimata, consectetur volutpat nam placerat augue sed accusam eu takimata odio dignissim deserunt illum te laboris reprehenderit rebum nostrud. At ad iusto esse nihil feugait iriure aliquam nisi commodo quis augue dolore. Fugiat eum laborum.', ],
                [ 10, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',  'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Est mollit congue eros anim fugiat vero excepteur eu. Nostrud cillum quod elitr wisi iusto quis proident gubergren ad non rebum. Mazim aliquam tincidunt eu nostrud invidunt suscipit. Eum clita enim. Eirmod accumsan voluptua. Eirmod cillum exercitation.',  ],
                [ 11, 2, 'Lorem ipsum dolor sit amet',                                'Consetetur accumsan, nobis aliquip dolore fugiat dolores nibh aliqua vel autem, takimata ut reprehenderit justo iure eros commodo suscipit quod minim option minim cillum nonumy. Nibh mazim euismod vel feugiat nisl feugait dolores justo cupiditat in nibh eos. Magna justo consequat veniam, accusam sunt aliqua accumsan tincidunt aliqua congue rebum aliquip adipiscing sed justo tempor sint aute eleifend iriure. Nisl ipsum dignissim.', ],
                [ 12, 2, 'Lorem ipsum',                                               'Sea volutpat et voluptua te obcaecat gubergren te iure aliquip ex proident deserunt nibh sadipscing, laoreet eros ullamcorper, officia accusam vel possim id laoreet gubergren officia placerat consectetur quis incidunt praesent gubergren stet mazim eros. Accumsan illum elit cillum, adipisici iusto kasd exercitation nisi sunt illum te ut aliquyam vel imperdiet. Option laborum ea nonummy stet vulputate iriure ipsum id dolor labore lorem takimata, consectetur volutpat nam placerat augue sed accusam eu takimata odio dignissim deserunt illum te laboris reprehenderit rebum nostrud. At ad iusto esse nihil feugait iriure aliquam nisi commodo quis augue dolore. Fugiat eum laborum.', ],
                [ 13, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',  'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Est mollit congue eros anim fugiat vero excepteur eu. Nostrud cillum quod elitr wisi iusto quis proident gubergren ad non rebum. Mazim aliquam tincidunt eu nostrud invidunt suscipit. Eum clita enim. Eirmod accumsan voluptua. Eirmod cillum exercitation.',  ],
                [ 14, 2, 'Lorem ipsum dolor sit amet',                                'Consetetur accumsan, nobis aliquip dolore fugiat dolores nibh aliqua vel autem, takimata ut reprehenderit justo iure eros commodo suscipit quod minim option minim cillum nonumy. Nibh mazim euismod vel feugiat nisl feugait dolores justo cupiditat in nibh eos. Magna justo consequat veniam, accusam sunt aliqua accumsan tincidunt aliqua congue rebum aliquip adipiscing sed justo tempor sint aute eleifend iriure. Nisl ipsum dignissim.', ],
                [ 15, 2, 'Lorem ipsum',                                               'Sea volutpat et voluptua te obcaecat gubergren te iure aliquip ex proident deserunt nibh sadipscing, laoreet eros ullamcorper, officia accusam vel possim id laoreet gubergren officia placerat consectetur quis incidunt praesent gubergren stet mazim eros. Accumsan illum elit cillum, adipisici iusto kasd exercitation nisi sunt illum te ut aliquyam vel imperdiet. Option laborum ea nonummy stet vulputate iriure ipsum id dolor labore lorem takimata, consectetur volutpat nam placerat augue sed accusam eu takimata odio dignissim deserunt illum te laboris reprehenderit rebum nostrud. At ad iusto esse nihil feugait iriure aliquam nisi commodo quis augue dolore. Fugiat eum laborum.', ],
                [ 16, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',  'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Est mollit congue eros anim fugiat vero excepteur eu. Nostrud cillum quod elitr wisi iusto quis proident gubergren ad non rebum. Mazim aliquam tincidunt eu nostrud invidunt suscipit. Eum clita enim. Eirmod accumsan voluptua. Eirmod cillum exercitation.',  ],
            ]);
            foreach($articles as $article){
                $article_repo->save( $article );
            }
            
            $comment_repo = new Repository( DB::getInstance(), Comment::class );
            foreach( Comment::fabricRandom( 100 ) as $comment ){
                $comment_repo->save( $comment );
            }
            
        }catch( \Exception $exception){
            echo $exception->getMessage() . "\n";
        }
    }
    
    public function methodDropDB()
    {
        $this->migrator
            ->setSchemas( $this->migrator->getCurrentSchemas() )
            ->dropSchema();
    }
    
    public function methodShowTables(): void
    {
        $table_names = ( (new DBMigratorGateway(
                DB::getInstance()
            ))->getTablesNames());
        
        foreach( $table_names as $table_name ){
            echo "$table_name\n";
        }
    }
    
    public function methodCheckDB(): void
    {
        $domains_schema_provider = new DomainsSchemaProvider(
            __DIR__ . '/../../Entities',
                'Entities'
        );
        
        $this->migrator
            ->setSchemas( $domains_schema_provider->getDomainsSchemas() )
            ->compareWithCurrentStructure();
        
        echo "-- Tables to create:\n";
        foreach($this->migrator->getNotExistingTables() as $table){
            echo "$table\n";
        }
        
        echo "\n -- Tables to update:\n";
        foreach($this->migrator->getTablesToUpdate() as $table){
            echo "$table\n";
        }
    }
}