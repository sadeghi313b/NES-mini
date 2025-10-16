use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->unsignedSmallInteger('cable_id')->nullable()->index();
            $table->unsignedTinyInteger('product_length')->nullable();
            $table->unsignedTinyInteger('tall_strip_length')->nullable();
            $table->unsignedTinyInteger('short_strip_length')->nullable();
            $table->unsignedTinyInteger('remaining_blue')->nullable();
            $table->unsignedTinyInteger('remaining_brown')->nullable();
            $table->unsignedTinyInteger('remaining_yellow')->nullable();
            $table->unsignedTinyInteger('blue_insulation_length')->nullable();
            $table->unsignedTinyInteger('brown_insulatioon_length')->nullable();
            $table->unsignedTinyInteger('yellow_insulation_length')->nullable();
            $table->unsignedSmallInteger('blue_terminal_id')->nullable()->index();
            $table->unsignedSmallInteger('brown_terminal_id')->nullable()->index();
            $table->unsignedSmallInteger('yellow_terminal_id')->nullable()->index();
            $table->unsignedSmallInteger('double_terminal_id')->nullable()->index();
            $table->unsignedSmallInteger('plug_id')->nullable()->index();
            $table->unsignedSmallInteger('cord_id')->nullable()->index();
            $table->unsignedTinyInteger('extra_earth_length')->default(0);
            $table->unsignedTinyInteger('cord_length')->default(0);

            // General fields
            $table->text('description')->nullable()->comment('searchable');
            $table->json('tags')->nullable()->comment('searchable');
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
