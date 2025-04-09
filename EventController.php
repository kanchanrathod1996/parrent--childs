<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Children;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;
use Auth;
use DB;
use Illuminate\Validation\Rule;
use App\Mail\Frontend\EventCreatedMaill;

class EventController extends Controller
{

    public function index(){

        $events = Event::get();
        return view('frontend.events.index',compact('events'));
    }
    public function create()
    {
        return view('frontend.events.create'); 
    }

    // public function review(Request $request){
    //     $data= $request->all();
       
    //     return view('frontend.events.create',compact('data'));
    // }
    public function store(Request $request)
    {
        // Validate inputs
       
        $validator = Validator::make($request->all(), [
            'member_name' => 'required',
            'acc' => 'required',
            'home_address' => 'required|string|regex:/^[A-Z\s]+$/', // Only capital letters
            'email' => 'required|email|unique:events,email',
            'phone' => 'required|string|max:15',
            'location' => 'required',
            'child_name.*' => 'required',
            'gender.*' => 'required|in:male,female',
            'age.*' => 'required|integer|min:0|max:17',
        ]);

          if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }   
  
        $eventData = $request->only(['member_name', 'acc', 'home_address', 'email', 'phone', 'location']);
        $event = Event::create($eventData); 
      
        if ($event) {  //query successful
        

    //    here children table 
        if ($request->has('child_name')) {
        	
            foreach ($request->child_name as $index => $name) {

                if (!empty($name)) {
                   
                    $event->children()->create([
                        'child_name' => $name,
                        'gender' => $request->gender[$index] ?? null,
                        'age' => $request->age[$index] ?? null,
                    ]);
                }
            }
        }
     
        $this->generatePdf($event);// here call the function generatePdf
          return $this->view($event->id);  //here call the view function
    }else{
        return redirect()->back()->with('error_message', 'Failed to create the event.');
    }   
    }



    public function view($id){
        
        $event = Event::with('children')->findOrFail($id);
        return view('frontend.events.view',compact('event'));
    }

    public function edit($id)
    {
       $event = Event::with('children')->findOrFail($id);
       return view('frontend.events.create', ['data' => $event]);
    }
   
    public function update(Request $request, $id)
{
  
    // Validate input
    $validator = Validator::make($request->all(), [
        // 'member_name' => 'required',
        'member_name' => ['required', Rule::unique('events', 'member_name')->ignore($id)],
        'acc' => 'required',
        'home_address' => 'required|string|regex:/^[A-Z\s]+$/', // Only capital letters
        // 'email' => 'required|email|unique:events,email,' . $id,
        'phone' => 'required|string|max:15',
        'location' => 'required',
        'child_name.*' => 'required',
        'gender.*' => 'required|in:male,female',
        'age.*' => 'required|integer|min:0|max:17',
    ]);
    

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }   
     
    // Update event
    $event = Event::findOrFail($id);
    $event->update($request->only(['member_name', 'acc', 'home_address', 'email', 'phone', 'location']));

   if($event){
    if ($request->has('child_name')) {

        $event->children()->delete();
    //  dd($event,$request->child_name);
        foreach ($request->child_name as $index => $name) {
                
            if (!empty($name)) {
                
                 $event->children()->create([
                        'child_name' => $name,
                        'gender' => $request->gender[$index] ?? null,
                        'age' => $request->age[$index] ?? null,
                    ]);

               
                // if (isset($existingChildren[$index])) {
                //     $event->children()->where('id', $existingChildren[$index])->update([
                //         'child_name' => $name,
                //         'gender' => $request->gender[$index] ?? null,
                //         'age' => $request->age[$index] ?? null,
                //     ]);
                // } else {
                   
                //     $event->children()->create([
                //         'child_name' => $name,
                //         'gender' => $request->gender[$index] ?? null,
                //         'age' => $request->age[$index] ?? null,
                //     ]);
                // }
            }
        }
        //   dd($request->child_name,$name,$event->children);
    
  
        // $childrenToDelete = array_diff($existingChildren['name'], array_keys($request->child_name));

        // dd($childrenToDelete,$existingChildren,$request->child_name);
        // if (!empty($childrenToDelete)) {
        //     $event->children()->whereIn('id', $childrenToDelete)->delete();
        // }
    //   dd($event);
    }

    }else{
        return redirect()->back()->with('error_message', 'Failed to create the event.');
    }
    
    return redirect()->route('events.view',$event->id)->with('success_message', 'Event updated successfully!');
  
}



    public function generatePdf(Event $event)
    {   
        $pdfDirectory = storage_path('app/public/events'); 
        if (!file_exists($pdfDirectory)) {
            mkdir($pdfDirectory, 0755, true); 
        }

        $mpdf = new Mpdf();
        $mpdf->WriteHTML(view('frontend.events.pdf', ['event' => $event])->render());

        $pdfPath = $pdfDirectory . '/event_' . $event->id . '.pdf'; 
        $mpdf->Output($pdfPath, 'F');
//     $rrr=$event->toArray();
//     var_dump($rrr);
//     echo"<br>";
// var_dump($event);
// exit;

        Mail::to($event->email)->send(new EventCreatedMaill([$pdfPath]));
    }


}
