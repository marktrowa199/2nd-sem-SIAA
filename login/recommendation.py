import sys
import json
import numpy as np
import tensorflow as tf

# Function to simulate loading a trained model (replace with actual loading logic)
def load_model():
    try:
        # Example of loading a TensorFlow model (you must train your model first and save it)
        model = tf.keras.models.load_model('event_recommendation_model.h5')
        return model
    except Exception as e:
        print(f"Error loading model: {e}", file=sys.stderr)
        return None

# Function to preprocess event data into features for prediction
def preprocess_event_data(event_data):
    # Here you would process the event data (e.g., convert text to embeddings, normalize features, etc.)
    # For this example, let's assume we're using some numeric features for the recommendation system.
    
    # This is just an example; adjust this based on your actual data
    features = np.array([event_data.get('event_id', 0), event_data.get('event_category', 0)])
    
    # Make sure it has the correct shape for prediction (e.g., (1, num_features))
    return features.reshape(1, -1)

# Main function to generate recommendations
def generate_recommendation(events_data):
    # Load pre-trained TensorFlow model
    model = load_model()
    if model is None:
        return {"error": "Failed to load recommendation model"}
    
    # Example: Use the first event as the input for recommendation logic
    event = events_data[0] if events_data else None
    if event is None:
        return {"error": "No event data available for recommendation"}
    
    # Preprocess the event data (you should adapt this to match your model's input requirements)
    event_features = preprocess_event_data(event)
    
    # Predict whether the event is recommended (use your model's prediction logic)
    try:
        prediction = model.predict(event_features)
        # Assuming the output is a probability or score; you may adjust this as needed
        recommended = prediction[0] > 0.5  # Example threshold for recommendation
        
        # Construct the response
        recommendation_result = {
            "event_name": event.get('event_name', 'Unknown Event'),
            "recommended": recommended
        }
        
        return recommendation_result
    
    except Exception as e:
        print(f"Error during prediction: {e}", file=sys.stderr)
        return {"error": "Error during recommendation generation"}

# Main entry point for the script
if __name__ == "__main__":
    try:
        # Read event data passed from PHP via sys.argv
        events_data = json.loads(sys.argv[1])  # Assuming JSON is passed as a string
        
        # Generate a recommendation based on the event data
        recommendation = generate_recommendation(events_data)
        
        # Output the recommendation as a JSON string
        print(json.dumps(recommendation))
    
    except Exception as e:
        print(f"Error processing input: {e}", file=sys.stderr)
        # Provide a meaningful error response if something goes wrong
        error_response = {"error": "Invalid input or processing error"}
        print(json.dumps(error_response))
